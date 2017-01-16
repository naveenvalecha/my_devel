<?php

/**
 * @file
 * Drush commands for the My Devel module.
 */

/**
 * Implements hook_drush_command().
 */
function my_devel_drush_command() {
  $items = [];

  // Example output:
  // @code
  // $ drush file-field-report
  //  Field                     Type   File directory
  //  node.article.field_image  image  [date:custom:Y]-[date:custom:m]
  //  user.user.user_picture    image  pictures/[date:custom:Y]-[date:custom:m]
  // @endcode
  $items['my-devel-file-field-report'] = [
    'description' => 'Generate a report on file fields.',
    'examples' => [
      'drush file-field-report --format=csv > report.csv' => 'Save the report to a CSV file.',
    ],
    'aliases' => ['file-field-report'],
    'outputformat' => [
      'field-labels' => [
        'field' => 'Field',
        'type' => 'Type',
        'file_directory' => 'File directory',
      ],
      'output-data-type' => 'format-table',
    ],
  ];

  // Test user commands.
  $items['my-devel-test-users-create'] = [
    'description' => 'Create a test user for each custom user role.',
    'aliases' => [
      'test-users-create',
      'create-test-users',
    ],
  ];
  $items['my-devel-test-users-delete'] = [
    'description' => 'Delete test users created by `my-devel-create-test-users`.',
    'aliases' => [
      'test-users-delete',
      'delete-test-users',
    ],
  ];
  $items['my-devel-test-users-list'] = [
    'description' => 'List existing test users.',
    'outputformat' => [
      'default' => 'list',
    ],
    'aliases' => [
      'test-users-list',
      'list-test-users',
    ],
  ];

  return $items;
}

/**
 * Implements hook_drush_help().
 */
function my_devel_drush_help($section) {
  switch ($section) {
    case 'meta:my_devel:title':
      return dt('My Devel commands');

    case 'drush:my-devel-test-users-create':
      return dt("Create a test user for each custom user role, with username and password both equal to the role machine name, e.g., 'page_creator'.");
  }
}

/**
 * Implements drush_hook_COMMAND().
 */
function drush_my_devel_file_field_report() {
  $ids = \Drupal::entityQuery('field_config')
    ->condition('field_type', ['file', 'image'], 'IN')
    ->execute();
  /** @var \Drupal\field\FieldConfigInterface[] $fields */
  $fields = \Drupal::entityTypeManager()
    ->getStorage('field_config')
    ->loadMultiple($ids);
  $result = [];
  foreach ($fields as $id => $field) {
    $result[] = [
      'field' => $field->id(),
      'type' => $field->getType(),
      'file_directory' => $field->getSetting('file_directory'),
    ];
  }
  return $result;
}

/**
 * Implements drush_hook_COMMAND().
 */
function drush_my_devel_test_users_create() {
  my_devel_test_users_create();
}

/**
 * Implements drush_hook_COMMAND().
 */
function drush_my_devel_test_users_delete() {
  my_devel_test_users_delete();
}

/**
 * Implements drush_hook_COMMAND().
 */
function drush_my_devel_test_users_list() {
  return my_devel_test_users_get_names();
}
