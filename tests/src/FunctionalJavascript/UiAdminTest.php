<?php

namespace Drupal\Tests\editor_advanced_image\FunctionalJavascript;

use Drupal\filter\Entity\FilterFormat;

/**
 * Tests ckeditor formats admin forms.
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_ui_admin
 */
class UiAdminTest extends UiTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'filter',
    'editor',
    'ckeditor',
    'editor_advanced_image',
  ];

  /**
   * We use the minimal profile because we want to test local action links.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * A user with the 'administer filters' permission.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create text format.
    $full_html_format = FilterFormat::create([
      'format'  => 'full_html',
      'name'    => 'Full HTML',
      'weight'  => 0,
      'filters' => [],
    ]);
    $full_html_format->save();

    // Create a user for tests.
    $this->adminUser = $this->drupalCreateUser(['administer filters']);
  }

  /**
   * Tests the node add page is reachable.
   */
  public function testAdminFormatsManageReachable() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/config/content/formats/manage/full_html');
    $this->isSuccessful();
  }

  /**
   * Tests a CKEditor editor and visibilit of Editor Advanced Image config.
   */
  public function testAdminForm() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/config/content/formats/manage/full_html');

    // Select the "CKEditor" editor and click the "Save configuration" button.
    $this->fillField('Text editor', 'ckeditor');

    // Wait on CKEditor Ajax call to load plugins forms.
    $this->assertSession()->assertWaitOnAjaxRequest();

    // Check the Editor Advanced Image tab is visible.
    $this->assertElementPresent('.vertical-tabs');

    // Ensure the default class is initialized to the expected default value.
    $this->assertFieldByName('editor[settings][plugins][editoradvancedimage][default_class]', '');
  }

  /**
   * Tests a CKEditor editor & storage of default class field.
   */
  public function testDefaultClass() {
    $this->testAdminForm();

    $this->fillField('editor[settings][plugins][editoradvancedimage][default_class]', 'my-class');

    // Submit the new value.
    $this->pressButton('edit-actions-submit');

    // Return on the editor configuration.
    $this->drupalGet('admin/config/content/formats/manage/full_html');

    // Ensure the previously filled data has been stored.
    $this->assertFieldByName('editor[settings][plugins][editoradvancedimage][default_class]', 'my-class');
  }

}
