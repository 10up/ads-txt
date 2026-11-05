<?php
/**
 * Abilities API integration for Ads.txt.
 *
 * Exposes reading, validating and updating ads.txt and app-ads.txt
 * to AI agents and other clients through the Abilities API.
 *
 * @package Ads_Txt_Manager
 */

namespace AdsTxt\Abilities;

use WP_Error;

const CATEGORY         = 'ads-txt';
const GET_ABILITY      = 'ads-txt/get-content';
const VALIDATE_ABILITY = 'ads-txt/validate-content';
const UPDATE_ABILITY   = 'ads-txt/update-content';

/**
 * Most characters allowed in the content input.
 *
 * Validating a file uses far more memory than its size, so an unlimited input
 * could exhaust PHP memory or exceed the database packet size.
 */
const MAX_CONTENT_LENGTH = 500000;

/**
 * Register the hooks.
 *
 * The plugin requires WordPress 6.9, which includes the Abilities API.
 *
 * @since 1.5.0
 *
 * @return void
 */
function setup() {
	add_action( 'wp_abilities_api_categories_init', __NAMESPACE__ . '\register_category' );
	add_action( 'wp_abilities_api_init', __NAMESPACE__ . '\register_abilities' );
}

/**
 * Register the ability category.
 *
 * @since 1.5.0
 *
 * @return void
 */
function register_category() {
	wp_register_ability_category(
		CATEGORY,
		array(
			'label'       => __( 'Ads.txt Manager', 'ads-txt' ),
			'description' => __( 'Abilities for reading, checking and updating the ads.txt and app-ads.txt files.', 'ads-txt' ),
		)
	);
}

/**
 * Meta shared by every ability.
 *
 * @since 1.5.0
 *
 * @param bool $readonly Whether the ability only reads data.
 *
 * @return array
 */
function get_meta( $readonly ) {
	return array(
		'show_in_rest' => true,
		// Opt in to the MCP Adapter so AI agents can discover the ability.
		'mcp'          => array(
			'public' => true,
		),
		'annotations'  => array(
			'readonly'    => $readonly,
			// Saving keeps revisions and deletes nothing.
			'destructive' => false,
			'idempotent'  => true,
		),
	);
}

/**
 * Schema for the `type` input.
 *
 * @since 1.5.0
 *
 * @return array
 */
function get_type_schema() {
	return array(
		'description' => __( 'Which file to use: adstxt for ads.txt or app-adstxt for app-ads.txt.', 'ads-txt' ),
		'type'        => 'string',
		'enum'        => array( 'adstxt', 'app-adstxt' ),
		'default'     => 'adstxt',
	);
}

/**
 * Schema for the list of errors found in a file.
 *
 * @since 1.5.0
 *
 * @return array
 */
function get_errors_schema() {
	return array(
		'description' => __( 'Errors found. Each has the line number, an error type and sometimes the value that failed.', 'ads-txt' ),
		'type'        => 'array',
		'items'       => array(
			'type'       => 'object',
			'properties' => array(
				'line'  => array(
					'description' => __( 'Line number of the error, starting at 1.', 'ads-txt' ),
					'type'        => 'integer',
				),
				'type'  => array(
					'description' => __( 'Error type, for example invalid_record or invalid_exchange.', 'ads-txt' ),
					'type'        => 'string',
				),
				'value' => array(
					'description' => __( 'The value that failed, when there is one.', 'ads-txt' ),
					'type'        => 'string',
				),
			),
			'required'   => array( 'line', 'type' ),
		),
	);
}

/**
 * Schema for the list of warnings found in a file.
 *
 * @since 1.5.0
 *
 * @return array
 */
function get_warnings_schema() {
	return array(
		'description' => __( 'Warnings found. Warnings never stop a file from being saved.', 'ads-txt' ),
		'type'        => 'array',
		'items'       => array(
			'type'       => 'object',
			'properties' => array(
				'type'    => array(
					'description' => __( 'Warning type, for example no_authorized_seller.', 'ads-txt' ),
					'type'        => 'string',
				),
				'message' => array(
					'description' => __( 'A readable description of the warning.', 'ads-txt' ),
					'type'        => 'string',
				),
			),
			'required'   => array( 'type' ),
		),
	);
}

/**
 * Register the abilities.
 *
 * @since 1.5.0
 *
 * @return void
 */
function register_abilities() {
	wp_register_ability(
		GET_ABILITY,
		array(
			'label'               => __( 'Get ads.txt content', 'ads-txt' ),
			'description'         => __( 'Returns the saved content of ads.txt or app-ads.txt, with the errors and warnings found when it was last saved.', 'ads-txt' ),
			'category'            => CATEGORY,
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'type' => get_type_schema(),
				),
				'additionalProperties' => false,
				// Lets clients call the ability with no input at all.
				'default'              => array(),
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'type'     => array( 'type' => 'string' ),
					'content'  => array(
						'description' => __( 'The saved file content. Empty when nothing is saved yet.', 'ads-txt' ),
						'type'        => 'string',
					),
					'errors'   => get_errors_schema(),
					'warnings' => get_warnings_schema(),
				),
				'required'   => array( 'type', 'content' ),
			),
			'execute_callback'    => __NAMESPACE__ . '\execute_get',
			'permission_callback' => __NAMESPACE__ . '\permission_check',
			'meta'                => get_meta( true ),
		)
	);

	wp_register_ability(
		VALIDATE_ABILITY,
		array(
			'label'               => __( 'Validate ads.txt content', 'ads-txt' ),
			'description'         => __( 'Checks text against the ads.txt rules and returns the errors and warnings. It saves nothing.', 'ads-txt' ),
			'category'            => CATEGORY,
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'content' => array(
						'description' => __( 'The file content to check.', 'ads-txt' ),
						'type'        => 'string',
						'maxLength'   => MAX_CONTENT_LENGTH,
					),
				),
				'required'             => array( 'content' ),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'valid'     => array(
						'description' => __( 'True when no errors were found.', 'ads-txt' ),
						'type'        => 'boolean',
					),
					'sanitized' => array(
						'description' => __( 'The content as it would be saved.', 'ads-txt' ),
						'type'        => 'string',
					),
					'errors'    => get_errors_schema(),
					'warnings'  => get_warnings_schema(),
				),
				'required'   => array( 'valid', 'sanitized', 'errors', 'warnings' ),
			),
			'execute_callback'    => __NAMESPACE__ . '\execute_validate',
			'permission_callback' => __NAMESPACE__ . '\permission_check',

			/*
			 * This ability saves nothing, but it is not marked read-only on purpose.
			 * Core sends read-only abilities as GET with the input in the URL, which
			 * cannot carry a large ads.txt. Any other ability is sent as POST.
			 */
			'meta'                => get_meta( false ),
		)
	);

	wp_register_ability(
		UPDATE_ABILITY,
		array(
			'label'               => __( 'Update ads.txt content', 'ads-txt' ),
			'description'         => __( 'Replaces the content of ads.txt or app-ads.txt. The content is validated first. If errors are found, nothing is saved unless force is true. Warnings never stop a save. Earlier versions are kept as revisions.', 'ads-txt' ),
			'category'            => CATEGORY,
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'type'    => get_type_schema(),
					'content' => array(
						'description' => __( 'The full new file content. It replaces the existing content.', 'ads-txt' ),
						'type'        => 'string',
						'maxLength'   => MAX_CONTENT_LENGTH,
					),
					'force'   => array(
						'description' => __( 'Save even when errors are found.', 'ads-txt' ),
						'type'        => 'boolean',
						'default'     => false,
					),
				),
				'required'             => array( 'content' ),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'saved'     => array(
						'description' => __( 'Whether the content was saved.', 'ads-txt' ),
						'type'        => 'boolean',
					),
					'sanitized' => array(
						'description' => __( 'The content as it was, or would have been, saved.', 'ads-txt' ),
						'type'        => 'string',
					),
					'errors'    => get_errors_schema(),
					'warnings'  => get_warnings_schema(),
				),
				'required'   => array( 'saved', 'sanitized', 'errors', 'warnings' ),
			),
			'execute_callback'    => __NAMESPACE__ . '\execute_update',
			'permission_callback' => __NAMESPACE__ . '\permission_check',
			'meta'                => get_meta( false ),
		)
	);
}

/**
 * Permission check shared by all abilities.
 *
 * Uses the same capability as the settings screens.
 *
 * @since 1.5.0
 *
 * @return bool
 */
function permission_check() {
	return current_user_can( ADS_TXT_MANAGE_CAPABILITY );
}

/**
 * Normalize the `type` input.
 *
 * @since 1.5.0
 *
 * @param array|null $input Ability input.
 *
 * @return string Either `adstxt` or `app-adstxt`.
 */
function get_type( $input ) {
	return is_array( $input ) && isset( $input['type'] ) && 'app-adstxt' === $input['type'] ? 'app-adstxt' : 'adstxt';
}

/**
 * Get the ID of the post that stores a file, if it exists.
 *
 * @since 1.5.0
 *
 * @param string $type Either `adstxt` or `app-adstxt`.
 *
 * @return int Post ID, or 0 when none is saved yet.
 */
function get_stored_post_id( $type ) {
	$option  = 'app-adstxt' === $type ? APP_ADS_TXT_MANAGER_POST_OPTION : ADS_TXT_MANAGER_POST_OPTION;
	$post_id = (int) get_option( $option );
	$post    = $post_id ? get_post( $post_id ) : null;

	return $post && $post->post_type === $type ? $post_id : 0;
}

/**
 * Run the get-content ability.
 *
 * @since 1.5.0
 *
 * @param array|null $input Ability input.
 *
 * @return array
 */
function execute_get( $input = null ) {
	$type    = get_type( $input );
	$post_id = get_stored_post_id( $type );
	$result  = array(
		'type'     => $type,
		'content'  => '',
		'errors'   => array(),
		'warnings' => array(),
	);

	if ( $post_id ) {
		$errors   = get_post_meta( $post_id, 'adstxt_errors', true );
		$warnings = get_post_meta( $post_id, 'adstxt_warnings', true );

		$result['content']  = (string) get_post_field( 'post_content', $post_id, 'raw' );
		$result['errors']   = is_array( $errors ) ? array_values( $errors ) : array();
		$result['warnings'] = is_array( $warnings ) ? array_values( $warnings ) : array();
	}

	return $result;
}

/**
 * Run the validate-content ability.
 *
 * @since 1.5.0
 *
 * @param array|null $input Ability input.
 *
 * @return array
 */
function execute_validate( $input = null ) {
	$content   = is_array( $input ) && isset( $input['content'] ) ? (string) $input['content'] : '';
	$validated = \AdsTxt\validate_content( $content );

	return array(
		'valid'     => empty( $validated['errors'] ),
		'sanitized' => $validated['sanitized'],
		'errors'    => array_values( $validated['errors'] ),
		'warnings'  => array_values( $validated['warnings'] ),
	);
}

/**
 * Run the update-content ability.
 *
 * @since 1.5.0
 *
 * @param array|null $input Ability input.
 *
 * @return array|WP_Error
 */
function execute_update( $input = null ) {
	$type    = get_type( $input );
	$content = is_array( $input ) && isset( $input['content'] ) ? (string) $input['content'] : '';

	// Read force strictly. Direct calls and MCP clients can send the string "false", which is not empty.
	$force     = is_array( $input ) && isset( $input['force'] ) && filter_var( $input['force'], FILTER_VALIDATE_BOOLEAN );
	$validated = \AdsTxt\validate_content( $content );
	$result    = array(
		'saved'     => false,
		'sanitized' => $validated['sanitized'],
		'errors'    => array_values( $validated['errors'] ),
		'warnings'  => array_values( $validated['warnings'] ),
	);

	// Match the settings screen, which asks the user before saving a file with errors.
	if ( ! empty( $validated['errors'] ) && ! $force ) {
		return $result;
	}

	$post_id = get_stored_post_id( $type );
	$saved   = \AdsTxt\save_content( $type, $validated, $post_id );

	if ( is_wp_error( $saved ) || ! $saved ) {
		return new WP_Error(
			'save_failed',
			__( 'The file could not be saved.', 'ads-txt' ),
			array( 'status' => 500 )
		);
	}

	// A new post is created on the first save. Remember it, like the settings screen does.
	if ( ! $post_id ) {
		update_option( 'app-adstxt' === $type ? APP_ADS_TXT_MANAGER_POST_OPTION : ADS_TXT_MANAGER_POST_OPTION, $saved );
	}

	$result['saved'] = true;

	return $result;
}

setup();
