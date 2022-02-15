describe("Admin can login and make sure plugin is activated", () => {
  it("Can deactivate plugin and activate it back", () => {
    cy.visitAdminPage("plugins.php");
    cy.get("#deactivate-ads-txt, tr[data-slug=ads-txt] .deactivate a").click();
    cy.get("#activate-ads-txt, tr[data-slug=ads-txt] .activate a").click();
    cy.get("#deactivate-ads-txt, tr[data-slug=ads-txt] .deactivate a").should("be.visible");
  });
});
