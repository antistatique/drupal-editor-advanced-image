<?php

namespace Drupal\Tests\editor_advanced_image\FunctionalJavascript;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\editor\Entity\Editor;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\filter\Entity\FilterFormat;
use Drupal\node\Entity\NodeType;

/**
 * Tests event info pages and links.
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_ui_dialog
 */
class UiDialogTest extends UiTestBase {

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
   * Defines a CKEditor using the "Full HTML" filter.
   *
   * @var \Drupal\editor\Entity\EditorInterface
   */
  protected $editor;

  /**
   * Defines a "Full HTML" filter format.
   *
   * @var \Drupal\filter\Entity\FilterFormatInterface
   */
  protected $editorFilterFormat;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create text format.
    $this->editorFilterFormat = FilterFormat::create([
      'format'  => 'full_html',
      'name'    => 'Full HTML',
      'weight'  => 0,
      'filters' => [],
    ]);
    $this->editorFilterFormat->save();

    $this->editor = Editor::create([
      'format' => 'full_html',
      'editor' => 'ckeditor',
    ]);
    $settings['toolbar']['rows'] = [
      [
        [
          'name' => 'Image',
          'items' => [
            'DrupalImage',
          ],
        ],
      ],
    ];
    $this->editor->setSettings($settings);
    $this->editor->save();

    // Create a node type for testing.
    NodeType::create(['type' => 'page', 'name' => 'page'])->save();

    $field_storage = FieldStorageConfig::loadByName('node', 'body');

    // Create a body field instance for the 'page' node type.
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle'        => 'page',
      'label'         => 'Body',
      'settings'      => ['display_summary' => TRUE],
      'required'      => TRUE,
    ])->save();

    // Assign widget settings for the 'default' form mode.
    EntityFormDisplay::create([
      'targetEntityType' => 'node',
      'bundle'           => 'page',
      'mode'             => 'default',
      'status'           => TRUE,
    ])->setComponent('body', ['type' => 'text_textarea_with_summary'])
      ->save();

    // Create a user for tests.
    $this->adminUser = $this->drupalCreateUser([
      'administer nodes',
      'create page content',
      'use text format full_html',
    ]);
  }

  /**
   * Tests CKEditor button image still apprear, works & dialog open.
   */
  public function testImageBaseDialogWorks() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('node/add/page');
    $this->assertSession()->statusCodeEquals(200);

    // Asserts the Image button is present.
    $this->assertElementPresent('#cke_edit-body-0-value .cke_button__drupalimage');

    // Asserts the Image button is present.
    $this->clickOnElement('css', '.cke_button__drupalimage');
    $this->assertSession()->assertWaitOnAjaxRequest();

    $this->assertElementPresent('.ui-dialog');
    $this->assertSession()->elementContains('css', '.ui-dialog .ui-dialog-titlebar', 'Insert Image');
  }

  /**
   * Test the appearance of every EIA attributes when no filters enabled.
   */
  public function testFilterHtmlDisable() {
    // Disable the filter_html filter: allow *all *tags.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status' => 0,
    ]);
    $this->editorFilterFormat->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementPresent('.ui-dialog .form-item-attributes-title');
    $this->assertElementPresent('.ui-dialog .form-item-attributes-class');
    $this->assertElementPresent('.ui-dialog .form-item-attributes-id');
  }

  /**
   * Test the appearance of EIA attributes when no filters enabled/partial.
   */
  public function testFilterHtmlEnable() {
    // Enable the filter_html filter: only a few img attributes.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status'   => 1,
      'settings' => [
        'allowed_html' => '<img src alt data-entity-type data-entity-uuid data-align data-caption>',
      ],
    ]);
    $this->editorFilterFormat->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-title');
    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-class');
    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-id');

    // Enable the filter_html filter: only a title img attributes.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status'   => 1,
      'settings' => [
        'allowed_html' => '<img src alt data-entity-type data-entity-uuid data-align data-caption title>',
      ],
    ]);
    $this->editorFilterFormat->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementPresent('.ui-dialog .form-item-attributes-title');
    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-class');
    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-id');

    // Enable the filter_html filter: only a class img attributes.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status'   => 1,
      'settings' => [
        'allowed_html' => '<img src alt data-entity-type data-entity-uuid data-align data-caption class>',
      ],
    ]);
    $this->editorFilterFormat->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-title');
    $this->assertElementPresent('.ui-dialog .form-item-attributes-class');
    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-id');

    // Enable the filter_html filter: only a id img attributes.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status'   => 1,
      'settings' => [
        'allowed_html' => '<img src alt data-entity-type data-entity-uuid data-align data-caption id>',
      ],
    ]);
    $this->editorFilterFormat->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-title');
    $this->assertElementNotPresent('.ui-dialog .form-item-attributes-class');
    $this->assertElementPresent('.ui-dialog .form-item-attributes-id');

    // Enable the filter_html filter: only a id, class, title img attributes.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status'   => 1,
      'settings' => [
        'allowed_html' => '<img src alt data-entity-type data-entity-uuid data-align data-caption class id title>',
      ],
    ]);
    $this->editorFilterFormat->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementPresent('.ui-dialog .form-item-attributes-title');
    $this->assertElementPresent('.ui-dialog .form-item-attributes-class');
    $this->assertElementPresent('.ui-dialog .form-item-attributes-id');
  }

  /**
   * Test Default Class is shown when configured.
   */
  public function testDefaultClass() {
    // Disable the filter_html filter: allow *all *tags.
    $this->editorFilterFormat->setFilterConfig('filter_html', [
      'status' => 0,
    ]);
    $this->editorFilterFormat->save();

    // Add a default class in the settings.
    $settings = [
      'toolbar' => [
        'rows' => [
          [
            [
              'name' => 'Image',
              'items' => [
                'DrupalImage',
              ],
            ],
          ],
        ],
      ],
      'plugins' => [
        'editoradvancedimage' => [
          'default_class' => 'test-default-class',
        ],
      ],
    ];
    $this->editor->setSettings($settings);
    $this->editor->save();

    $this->testImageBaseDialogWorks();

    $this->assertElementPresent('.ui-dialog .form-item-attributes-class');
    $this->assertSession()->elementContains('css', '.ui-dialog .form-item-attributes-class', 'Default: <code>test-default-class</code>');
  }

}
