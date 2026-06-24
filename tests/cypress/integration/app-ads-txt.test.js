describe("Manage app-ads.txt", () => {
  const incorrectRecord = "test incorrect record";
  const correctRecord =
    "example.com, pub-00000000000, DIRECT, f08c47fec0942fa0";

  before(() => {
    cy.setPermalinkStructure("/%postname%/");
  });

  it("Can visit manage app-ads.txt page", () => {
    cy.visitAdminPage("options-general.php?page=app-adstxt-settings");
    cy.get("#wpbody h2").should("have.text", "Manage App-ads.txt");
  });

  it("Can update invalid record anyway", () => {
    cy.visitAdminPage("options-general.php?page=app-adstxt-settings");
    cy.get(".adstxt-settings-form .CodeMirror")
      .click()
      .type("{selectall}")
      .type(incorrectRecord);
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-notice-save-error").should(
      "contain.text",
      "Your app-ads.txt contains the following issues"
    );
    cy.get(".adstxt-settings-form #submit").should("be.disabled");
    cy.get("#adstxt-ays-checkbox").click();
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-saved").should("contain.text", "App-ads.txt saved");
    cy.get(".adstxt-notice-save-error").should(
      "contain.text",
      "Your app-ads.txt contains the following issues"
    );
  });

  it("Can save and visit correct app-ads.txt", () => {
    cy.visitAdminPage("options-general.php?page=app-adstxt-settings");
    cy.get(".adstxt-settings-form .CodeMirror")
      .click()
      .type("{selectall}")
      .type(correctRecord);
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-saved").should("contain.text", "App-ads.txt saved");
    cy.get(".adstxt-notice-save-error").should("not.exist");
    cy.request(`/app-ads.txt`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
    cy.request(`/app-ads.txt?cache-busting=1`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
    cy.request(`/app-ads.txt?`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
  });

  it("Can manage revisions", () => {
    cy.visitAdminPage("options-general.php?page=app-adstxt-settings");
    cy.get(".misc-pub-revisions a").should("contain.text", "Browse").click();
    cy.get(".long-header").should("contain.text", "Compare Revisions");

    // Trying to use the revisions UI in WordPress 7.0+
	// is causing crashes when run in CI. Since we technically
	// don't care about the actual UI, as WordPress is responsible
	// for displaying the correct content, we'll just visit the previous
	// revision's restore URL directly.
    cy.window().then((win) => {
      const revisions = win._wpRevisionsSettings.revisionData.filter(
        (revision) => !revision.autosave
      );
      const current = revisions.findIndex((revision) => revision.current);
      const previous = revisions[current - 1];

      expect(
        previous && previous.restoreUrl,
        "previous revision restore URL"
      ).to.be.a("string");

      cy.visit(previous.restoreUrl);
    });

    cy.get(".notice-success").should("contain.text", "Revision restored");
    cy.request(`/app-ads.txt`).then((response) => {
      expect(response.body).to.contain(incorrectRecord);
    });
  });
});
