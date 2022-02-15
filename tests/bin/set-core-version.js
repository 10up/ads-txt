#!/usr/bin/env node

const fs = require( 'fs' );

const path = `${ process.cwd() }/.wp-env.override.json`;

let config = fs.existsSync( path ) ? require( path ) : {};

const args = process.argv.slice( 2 );

if ( args.length == 0 ) return;

if ( args[ 0 ] == 'latest' ) return;

config.core = args[ 0 ];

try {
	fs.writeFileSync( path, JSON.stringify( config ) );
} catch ( err ) {
	console.error( err );
}
