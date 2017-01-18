<?php

/**
 * @file
 * Behat feature subcontext for the My Devel module.
 */

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;
use Drupal\my_devel\Behat\TableComparisonTrait;
use Drupal\user\Entity\Role;

// @todo Figure out how to make Behat use autoloader.
require_once __DIR__ . '/../../src/Behat/TableComparisonTrait.php';
require_once __DIR__ . '/../../src/Behat/UnequalTablesException.php';

/**
 * Provides a Behat sub-context for My Devel.
 */
class MyDevelFeatureSubContext extends DrupalSubContextBase implements DrupalSubContextInterface, SnippetAcceptingContext {

  use TableComparisonTrait;

  /**
   * Asserts that exactly a given list of roles exists (and no more).
   *
   * @Then exactly the following roles should exist
   */
  public function assertExactRolesExist(TableNode $expected) {
    $roles = [['id', 'label']];
    /** @var \Drupal\user\Entity\Role $role */
    foreach (Role::loadMultiple() as $id => $role) {
      $roles[] = [$id, $role->label()];
    }
    $actual = new TableNode($roles);

    $this->assertTableEquals($expected, $actual, 'Missing roles', 'Unexpected roles');
  }

  /**
   * Asserts that a given role is the administrator role.
   *
   * @Then the :role_id role should be the administrator role
   */
  public function assertRoleIsAdministrator($role_id) {
    /** @var \Drupal\user\RoleInterface $role */
    $role = Role::load($role_id);

    if (!$role) {
      throw new \Exception(sprintf('No such role: %s.', $role_id));
    }

    if (!$role->isAdmin()) {
      throw new \Exception(sprintf('The %s role is not the administrator role.', $role_id));
    }
  }

}
