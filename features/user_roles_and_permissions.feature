@api @my_devel
Feature: User roles and permissions
  Access to site functionality should be controlled with user roles and
  permissions.

  Scenario: User roles
    Then exactly the following roles should exist
      | id            | label              |
      | administrator | Administrator      |
      | anonymous     | Anonymous user     |
      | authenticated | Authenticated user |
    And the "administrator" role should be the administrator role
