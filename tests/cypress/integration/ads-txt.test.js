describe("Manage ads.txt", () => {
  const incorrectRecord = "test incorrect record";
  const correctRecord =
    "example.com, pub-00000000000, DIRECT, f08c47fec0942fa0";

  before(() => {
    cy.setPermalinkStructure("/%postname%/");
  });

  it("Can visit manage ads.txt page", () => {
    cy.visitAdminPage("options-general.php?page=adstxt-settings");
    cy.get("#wpbody h2").should("have.text", "Manage Ads.txt");
  });

  it("Can update invalid record anyway", () => {
    cy.visitAdminPage("options-general.php?page=adstxt-settings");
    cy.get(".adstxt-settings-form .CodeMirror")
      .click()
      .type("{selectall}")
      .type(incorrectRecord);
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-notice-save-error").should(
      "contain.text",
      "Your Ads.txt contains the following issues"
    );
    cy.get(".adstxt-settings-form #submit").should("be.disabled");
    cy.get("#adstxt-ays-checkbox").click();
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-saved").should("contain.text", "Ads.txt saved");
    cy.get(".adstxt-notice-save-error").should(
      "contain.text",
      "Your Ads.txt contains the following issues"
    );
  });

  it("Can save and visit correct ads.txt", () => {
    cy.visitAdminPage("options-general.php?page=adstxt-settings");
    cy.get(".adstxt-settings-form .CodeMirror")
      .click()
      .type("{selectall}")
      .type(correctRecord);
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-saved").should("contain.text", "Ads.txt saved");
    cy.get(".adstxt-notice-save-error").should("not.exist");
    cy.request(`/ads.txt`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
    cy.request(`/ads.txt?cache-busting=1`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
    cy.request(`/ads.txt?`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
  });

  it("Can manage revisions", () => {
    cy.visitAdminPage("options-general.php?page=adstxt-settings");

    // Rendering revision.php crashes the headless CI renderer in WordPress 7.0+
    cy.get(".misc-pub-revisions a")
      .should("contain.text", "Browse")
      .invoke("prop", "href")
      .then((compareUrl) => {
        cy.request(compareUrl).then((response) => {
          const match = response.body.match(/var _wpRevisionsSettings = (.+);/);
          expect(match, "_wpRevisionsSettings bootstrap").to.not.be.null;

          const revisions = JSON.parse(match[1]).revisionData.filter(
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
      });

    cy.get(".notice-success").should("contain.text", "Revision restored");
    cy.request(`/ads.txt`).then((response) => {
      expect(response.body).to.contain(incorrectRecord);
    });
  });
});
