@api @my_devel
Feature: User roles and permissions
  In order to specify access control details
  As a developer
  I want to have steps for user roles and permissions.

  Scenario: Specify user roles and permissions exactly
    Then exactly the following roles should exist
      | id            | label              |
      | administrator | Administrator      |
      | anonymous     | Anonymous user     |
      | authenticated | Authenticated user |
    And the "administrator" role should be the administrator role
    And the "anonymous" role should have exactly the following permissions
      | access comments                 |
      | access content                  |
      | access site-wide contact form   |
      | search content                  |
      | use text format restricted_html |
    And the "authenticated" role should have exactly the following permissions
      | access comments               |
      | access content                |
      | access shortcuts              |
      | access site-wide contact form |
      | post comments                 |
      | search content                |
      | skip comment approval         |
      | use text format basic_html    |

  Scenario: Specify ad hoc user roles and permissions
    Then the "anonymous" role should exist
