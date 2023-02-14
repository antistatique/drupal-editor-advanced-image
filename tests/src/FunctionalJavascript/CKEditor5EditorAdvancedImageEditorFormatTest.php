<?php

namespace Drupal\Tests\editor_advanced_image\FunctionalJavascript;

use Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage;
use Drupal\filter\Entity\FilterFormat;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\editor\Entity\Editor;
use Drupal\Tests\ckeditor5\Traits\CKEditor5TestTrait;
use Drupal\Tests\editor_advanced_image\Traits\CKEditor5InteractionTestTrait;

/**
 * Ensure the CKE5 editor_advanced_image editor formats forms.
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_functional
 * @group editor_advanced_image_ckeditor5
 *
 * @requires module ckeditor5
 */
class CKEditor5EditorAdvancedImageEditorFormatTest extends WebDriverTestBase {
  use CKEditor5TestTrait;
  use CKEditor5InteractionTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'ckeditor5',
    'editor_advanced_image',
  ];

  /**
   * The user to use during testing.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'starterkit_theme';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create an CK5 editor without the image upload enabled.
    FilterFormat::create([
      'format' => 'editor_without_image_upload',
      'name' => 'Test format without image upload',
      'filters' => [
        'filter_html' => [
          'status' => TRUE,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <em> <a href>',
          ],
        ],
      ],
    ])->save();
    Editor::create([
      'editor' => 'ckeditor5',
      'format' => 'editor_without_image_upload',
      'settings' => [
        'toolbar' => [
          'items' => [
            'link',
            'bold',
            'italic',
          ],
        ],
        'plugins' => [],
      ],
      'image_upload' => [
        'status' => FALSE,
      ],
    ])->save();

    // Create an CK5 editor with the image upload enabled.
    FilterFormat::create([
      'format' => 'editor_with_image_upload',
      'name' => 'Test format with image upload',
      'filters' => [
        'filter_html' => [
          'status' => TRUE,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <em> <a href>',
          ],
        ],
      ],
    ])->save();

    Editor::create([
      'editor' => 'ckeditor5',
      'format' => 'editor_with_image_upload',
      'settings' => [
        'toolbar' => [
          'items' => [
            'link',
            'bold',
            'italic',
            'drupalInsertImage',
          ],
        ],
        'plugins' => [
          'editor_advanced_image_image' => EditorAdvancedImage::DEFAULT_CONFIGURATION,
        ],
      ],
      'image_upload' => [
        'status' => TRUE,
        'scheme' => 'public',
        'directory' => 'inline-images',
        'max_size' => '',
        'max_dimensions' => [
          'width' => 0,
          'height' => 0,
        ],
      ],
    ])->save();

    $this->adminUser = $this->drupalCreateUser([
      'administer filters',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests that when the plugin CKE5 forms is visible on filter admin UI.
   */
  public function testImageEditorSettingsForm(): void {
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/config/content/formats/manage/editor_with_image_upload');

    // The editor advanced image plugin settings forms should be present.
    $assert_session->elementExists('css', '[data-drupal-selector="edit-editor-settings-plugins-editor-advanced-image-image"]');

    // Removing the drupalImageInsert button from the toolbar must remove the
    // plugin settings forms too.
    $this->triggerKeyUp('.ckeditor5-toolbar-item-drupalInsertImage', 'ArrowUp');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->elementNotExists('css', '[data-drupal-selector="edit-editor-settings-plugins-editor-advanced-image-image"]');

    // Re-adding the drupalImageInsert button to the toolbar must re-add the
    // plugin settings forms too.
    $this->triggerKeyUp('.ckeditor5-toolbar-item-drupalInsertImage', 'ArrowDown');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->elementExists('css', '[data-drupal-selector="edit-editor-settings-plugins-editor-advanced-image-image"]');
  }

  /**
   * Ensure when image uploads is enabled, default config of EAI is applied.
   *
   * Tests that when image uploads is enabled, then the class attribute of
   * Editor Advanced Image is enabled by default.
   */
  public function testImageEditorSettingsAllowedTags(): void {
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/config/content/formats/manage/editor_without_image_upload');
    $page = $this->getSession()->getPage();

    $allowed_html_field = $assert_session->fieldExists('filters[filter_html][settings][allowed_html]');
    $this->assertSame('<p> <br> <strong> <em> <a href>', $allowed_html_field->getValue());

    // Enable the image toolbar item.
    // Enabling image uploads adds <img> with several attributes allowed.
    $this->triggerKeyUp('.ckeditor5-toolbar-item-drupalInsertImage', 'ArrowDown');
    $assert_session->assertWaitOnAjaxRequest();
    $this->assertTrue($page->hasUncheckedField('editor[settings][plugins][ckeditor5_image][status]'));
    $page->checkField('editor[settings][plugins][ckeditor5_image][status]');
    $assert_session->assertWaitOnAjaxRequest();

    $allowed_html_field = $assert_session->fieldExists('filters[filter_html][settings][allowed_html]');

    // Assert that image uploads are enabled initially.
    $this->assertTrue($page->hasCheckedField('Enable image uploads'));

    // Assert that Editor Advanced Image attribute "class" is enabled by
    // default.
    $this->assertTrue($page->hasCheckedField('CSS classes (class)'));
    $this->assertFalse($page->hasCheckedField('Title (title)'));
    $this->assertFalse($page->hasCheckedField('ID (id)'));

    // The image insert plugin is enabled and inserting <img> is allowed.
    $this->assertSame('<br> <p> <strong> <em> <a href> <img src alt height width data-entity-uuid data-entity-type class>', $allowed_html_field->getValue());
  }

  /**
   * Ensure the plugin CKE5 forms is properly stored in database.
   */
  public function testImageEditorSettingsSave(): void {
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/config/content/formats/manage/editor_with_image_upload');
    $page = $this->getSession()->getPage();

    // Assert that image uploads are enabled initially.
    $this->assertTrue($page->hasCheckedField('Enable image uploads'));

    // Assert that Editor Advanced Image attribute "class" is enabled by
    // default.
    $this->assertFalse($page->hasCheckedField('Disable Balloon'));
    $this->assertTrue($page->hasCheckedField('CSS classes (class)'));
    $this->assertFalse($page->hasCheckedField('Title (title)'));
    $this->assertFalse($page->hasCheckedField('ID (id)'));

    // Click Editor Advanced Image vertical tab to make it interactable.
    $page->clickLink('Editor Advanced Image');

    // Enable the title attribute of Editor Advanced Image.
    $page->checkField('editor[settings][plugins][editor_advanced_image_image][enabled_attributes][title]');
    $assert_session->assertWaitOnAjaxRequest();
    $this->assertTrue($page->hasCheckedField('Title (title)'));

    // Set a default class.
    $page->fillField('editor[settings][plugins][editor_advanced_image_image][default_class]', 'img-responsive');

    // Save the form.
    $page->pressButton('Save configuration');
    $assert_session->pageTextContains('The text format Test format with image upload has been updated');

    // Flush caches so the updated config can be checked.
    drupal_flush_all_caches();

    // Confirm that the tags required by the newly-added plugins were correctly
    // saved.
    $this->drupalGet('admin/config/content/formats/manage/editor_with_image_upload');
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    // And for good measure, confirm the correct tags are in the form field when
    // returning to the form.
    $this->assertFalse($page->hasCheckedField('Disable Balloon'));
    $this->assertTrue($page->hasCheckedField('CSS classes (class)'));
    $this->assertTrue($page->hasCheckedField('Title (title)'));
    $allowed_html_field = $assert_session->fieldExists('filters[filter_html][settings][allowed_html]');
    $this->assertSame('<br> <p> <strong> <em> <a href> <img src alt height width data-entity-uuid data-entity-type title class>', $allowed_html_field->getValue());
    $default_class = $assert_session->fieldExists('editor[settings][plugins][editor_advanced_image_image][default_class]');
    $this->assertSame('img-responsive', $default_class->getValue());
  }

}
