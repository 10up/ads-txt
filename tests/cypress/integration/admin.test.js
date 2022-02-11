describe("Admin can login and make sure plugin is activated", () => {
  it("Can deactivate plugin and activate it back", () => {
    cy.visitAdminPage("plugins.php");
    cy.get("#deactivate-ads-txt").click();
    cy.get("#activate-ads-txt").click();
    cy.get("#deactivate-ads-txt").should("be.visible");
  });
});
