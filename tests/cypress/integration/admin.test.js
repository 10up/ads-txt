describe("Admin can login and make sure plugin is activated", () => {
  before(() => {
    cy.setPermalinkStructure("/%postname%/");
  });

  it("Permalink structure works", () => {
    cy.visit("/hello-world/");
  });

  it("Can deactivate plugin and activate it back", () => {
    cy.visitAdminPage("plugins.php");
    cy.get("#deactivate-ads-txt, tr[data-slug=ads-txt] .deactivate a").click();
    cy.get("#activate-ads-txt, tr[data-slug=ads-txt] .activate a").click();
    cy.get("#deactivate-ads-txt, tr[data-slug=ads-txt] .deactivate a").should(
      "be.visible"
    );
  });

  it("Can navigate to Ads.txt settings from the plugin list", () => {
    cy.visitAdminPage("plugins.php");

    cy.get('tr[data-slug="ads-txt"] .ads_txt_settings a')
      .should("be.visible")
      .click();

    cy.url().should("include", "options-general.php?page=adstxt-settings");
  });

  it("Can navigate to App-ads.txt settings from the plugin list", () => {
    cy.visitAdminPage("plugins.php");

    cy.get('tr[data-slug="ads-txt"] .app_ads_txt_settings a')
      .should("be.visible")
      .click();

    cy.url().should("include", "options-general.php?page=app-adstxt-settings");
  });
});
