<?php

namespace Drupal\Tests\nbsp\FunctionalJavascript;

use Drupal\editor\Entity\Editor;
use Drupal\file\Entity\File;
use Drupal\filter\Entity\FilterFormat;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\ckeditor5\Traits\CKEditor5TestTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Ensure the CKE5 editor_advanced_image balloon works.
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_functional
 * @group editor_advanced_image_ckeditor5
 *
 * @requires module ckeditor5
 */
class CKEditor5EditorAdvancedImageDialogTest extends WebDriverTestBase {

  use CKEditor5TestTrait;
  use TestFileCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'ckeditor5',
    'node',
    'text',
    'editor_advanced_image',
  ];

  /**
   * The sample image File entity to embed.
   *
   * @var \Drupal\file\FileInterface
   */
  protected $file;

  /**
   * The user to use during testing.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * The node to use during testing.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $testNode;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'starterkit_theme';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    FilterFormat::create([
      'format' => 'test_format',
      'name' => 'Test format',
    ])->save();

    Editor::create([
      'editor' => 'ckeditor5',
      'format' => 'test_format',
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
          'ckeditor5_editorAdvancedImage' => [
            'enabled_attributes' => [],
            'default_class' => '',
          ],
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

    // Create a sample host entity to embed images in.
    $this->file = File::create([
      'uri' => $this->getTestFiles('image')[0]->uri,
    ]);

    // Valid image.
    $img_tag = '<img ' . $this->imageAttributesAsString() . ' width="500" />';

    // Create a sample node to test EditorAdvancedImage on.
    $this->drupalCreateContentType(['type' => 'blog']);
    $this->testNode = $this->createNode([
      'type' => 'blog',
      'title' => 'Animals with strange names',
      'body' => [
        'value' => '<p><a href="https://en.wikipedia.org/wiki/Llama">Llamas</a> are cool!</p>' . $img_tag,
        'format' => 'test_format',
      ],
    ]);
    $this->testNode->save();

    $this->drupalLogin($this->drupalCreateUser([
      'use text format test_format',
      'bypass node access',
    ]));
  }

  /**
   * Provides the relevant image attributes.
   */
  private function imageAttributes(): array {
    return ['src' => '/core/misc/druplicon.png'];
  }

  /**
   * Helper to format attributes.
   *
   * @param bool $reverse
   *   Reverse attributes when printing them.
   */
  private function imageAttributesAsString($reverse = FALSE): string {
    $string = [];
    foreach ($this->imageAttributes() as $key => $value) {
      $string[] = $key . '="' . $value . '"';
    }
    if ($reverse) {
      $string = array_reverse($string);
    }
    return implode(' ', $string);
  }

  /**
   * Tests that EditorAdvancedImage enabled setting attr are usable into CKE5.
   *
   * @dataProvider providerAttributesTest
   */
  public function testAttributes(string $attribute_name, string $expected_input_label): void {
    // Update text format and editor to allow editing of this attribute through
    // the EditorAdvancedImage plugin.
    $editor = Editor::load('test_format');
    $settings = $editor->getSettings();

    $settings['plugins']['ckeditor5_editorAdvancedImage']['enabled_attributes'][] = $attribute_name;
    $editor->setSettings($settings)->save();

    $page = $this->getSession()->getPage();

    $this->drupalGet($this->testNode->toUrl('edit-form'));
    $this->waitForEditor();
    $assert_session = $this->assertSession();

    // Confirm the images widget exists.
    $this->assertNotEmpty($image_block = $assert_session->waitForElementVisible('css', ".ck-content .ck-widget.image"));

    // Open the Image balloon.
    $img = $assert_session->waitForElementVisible('css', '.ck-content img', 1000);
    $img->click();

    // Ensure that the Editor Advanced Image button is visible on the Image
    // Balloon.
    $this->assertNotEmpty($eai_button = $this->getBalloonButton('Editor Advanced Image'));
    $eai_button->click();

    // Ensure the enabled attribute will enable only the corresponding input.
    $balloon = $this->assertVisibleBalloon('.ck-editor-advanced-image');
    $this->assertTrue($balloon->hasField($expected_input_label));

    $eai_input = $page->find('css', '.ck-balloon-panel .ck-editor-advanced-image input[type=text]');
    $eai_input->setValue("foo-bar-{$attribute_name}");

    // Save the Balloon changes.
    $this->assertNotEmpty($save_button = $this->getBalloonButton('Save'));
    $save_button->click();

    // Save the node and confirm that the attribute text is retained.
    $page->pressButton('Save');
    $this->assertNotEmpty($assert_session->waitForElement('css', "img[{$attribute_name}=\"foo-bar-{$attribute_name}\"]"));

    // Ensure once re-open, the attribute value is reused as default value
    // on the input of Editor Advanced Image Balloon form.
    $this->drupalGet($this->testNode->toUrl('edit-form'));
    $this->waitForEditor();
    $assert_session = $this->assertSession();
    $img = $assert_session->waitForElementVisible('css', '.ck-content img', 1000);
    $img->click();
    $eai_button = $this->getBalloonButton('Editor Advanced Image');
    $eai_button->click();
    $balloon = $this->assertVisibleBalloon('.ck-editor-advanced-image');
    $balloon->hasField($expected_input_label);
    $eai_input = $page->find('css', '.ck-balloon-panel .ck-editor-advanced-image input[type=text]');
    self::assertSame("foo-bar-{$attribute_name}", $eai_input->getValue());
  }

  /**
   * Tests that EditorAdvancedImage default class feature works.
   */
  public function testDefaultClass(): void {
    // Update text format and editor to allow editing of the class attribute via
    // the EditorAdvancedImage plugin.
    $editor = Editor::load('test_format');
    $settings = $editor->getSettings();
    $settings['plugins']['ckeditor5_editorAdvancedImage']['enabled_attributes'][] = 'class';
    $settings['plugins']['ckeditor5_editorAdvancedImage']['default_class'] = 'img-responsive';
    $editor->setSettings($settings)->save();

    $page = $this->getSession()->getPage();

    $this->drupalGet($this->testNode->toUrl('edit-form'));
    $this->waitForEditor();
    $assert_session = $this->assertSession();

    // Confirm the images widget exists.
    $this->assertNotEmpty($image_block = $assert_session->waitForElementVisible('css', ".ck-content .ck-widget.image"));

    // Open the Image balloon.
    $image_block->click();

    // Ensure that the Editor Advanced Image button is visible on the Image
    // Balloon.
    $eai_button = $this->getBalloonButton('Editor Advanced Image');
    $eai_button->click();

    // Ensure the enabled attribute will enable only the corresponding input.
    $balloon = $this->assertVisibleBalloon('.ck-editor-advanced-image');
    $this->createScreenshot(\Drupal::root() . '/sites/default/files/simpletest/screen.png');

    // Ensure the class input will be filled with the default value.
    $eai_input = $page->find('css', '.ck-balloon-panel .ck-editor-advanced-image input[type=text]');
    self::assertSame("img-responsive", $eai_input->getValue());

    // Override the value with another more specific class that should be kept.
    $eai_input->setValue('img-fluid');

    // Save the Balloon changes.
    $this->assertNotEmpty($save_button = $this->getBalloonButton('Save'));
    $save_button->click();

    // Save the node and confirm that the attribute text is retained.
    $page->pressButton('Save');
    $this->assertNotEmpty($assert_session->waitForElement('css', 'img[class="img-fluid"]'));

    // Ensure once re-open, the attribute value is reused as default value
    // and replace the configured default_class.
    $this->drupalGet($this->testNode->toUrl('edit-form'));
    $this->waitForEditor();
    $assert_session = $this->assertSession();
    $img = $assert_session->waitForElementVisible('css', '.ck-content img', 1000);
    $img->click();
    $eai_button = $this->getBalloonButton('Editor Advanced Image');
    $eai_button->click();
    $balloon = $this->assertVisibleBalloon('.ck-editor-advanced-image');
    $balloon->hasField('class');
    $eai_input = $page->find('css', '.ck-balloon-panel .ck-editor-advanced-image input[type=text]');
    self::assertSame("img-fluid", $eai_input->getValue());
  }

  /**
   * A collection of attribute to enable and ensure works when enabled.
   */
  public function providerAttributesTest(): iterable {
    return [
      '<img title>' => [
        'attribute_name' => 'title',
        'input_label' => 'Title',
      ],
      '<img class>' => [
        'attribute_name' => 'class',
        'input_label' => 'CSS classes',
      ],
      '<img id>' => [
        'attribute_name' => 'id',
        'input_label' => 'ID',
      ],
    ];
  }

}
