
Feature: Books

  Scenario: List of books
    When I go to "/books"
    Then I should see a list of books
    And I should see "Peopleware"
    And I should see "Systemantics / The Systems Bible"


