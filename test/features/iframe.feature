
Feature: Iframe

  @wip
  Scenario: Editing form in iframe
    When I go to "/iframe/container"
    And I wait to see "Iframe contents"
    And I fill in "first_name" with "John"
    Then the "first_name" field should contain "John"




