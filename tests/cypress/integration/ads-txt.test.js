describe("Manage ads.txt", () => {
  const incorrectRecord = "test incorrect record";
  const correctRecord =
    "example.com, pub-00000000000, DIRECT, f08c47fec0942fa0";

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
    cy.get(".notice-error").should(
      "contain.text",
      "Your Ads.txt contains the following issues"
    );
    cy.get(".adstxt-settings-form #submit").should("be.disabled");
    cy.get("#adstxt-ays-checkbox").click();
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-saved").should("contain.text", "Ads.txt saved");
    cy.get(".notice-error").should(
      "contain.text",
      "Your Ads.txt contains the following issues"
    );
  });

  it("Can save correct ads.txt", () => {
    cy.setPermalinkStructure(`/%postname%/`);
    cy.visitAdminPage("options-general.php?page=adstxt-settings");
    cy.get(".adstxt-settings-form .CodeMirror")
      .click()
      .type("{selectall}")
      .type(correctRecord);
    cy.get(".adstxt-settings-form #submit").click();
    cy.get(".adstxt-saved").should("contain.text", "Ads.txt saved");
    cy.get(".notice-error").should("not.exist");
    cy.wait(2000);
    cy.request(`ads.txt`).then((response) => {
      expect(response.body).to.contain(correctRecord);
    });
  });

  it("Can manage revisions", () => {
    cy.setPermalinkStructure(`/%postname%/`);
    cy.visitAdminPage("options-general.php?page=adstxt-settings");
    cy.get(".misc-pub-revisions a").should("contain.text", "Browse").click();
    cy.get(".long-header").should("contain.text", "Compare Revisions");
    cy.get(".restore-revision.button").should("be.disabled");
    cy.get(".revisions-previous .button").click();
    cy.get(".restore-revision.button").should("be.enabled").click();
    cy.get(".notice-success").should("contain.text", "Revision restored");
    cy.wait(2000);
    cy.request(`ads.txt`).then((response) => {
      expect(response.body).to.contain(incorrectRecord);
    });
  });
});
