<?php

declare(strict_types=1);

namespace Drupal\Tests\editor_advanced_image\Unit;

use Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage;
use Drupal\editor\EditorInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversDefaultClass \Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_unit
 * @group editor_advanced_image_ckeditor5
 *
 * @internal
 */
class CKEditor5EditorAdvancedImagePluginTest extends UnitTestCase {

  /**
   * @covers ::getDynamicPluginConfig
   *
   * @dataProvider providerGetDynamicPluginConfig
   */
  public function testGetDynamicPluginConfig(array $configuration, array $expected_dynamic_config): void {
    // Read the CKEditor 5 plugin's static configuration from YAML.
    $ckeditor5_plugin_definitions = Yaml::parseFile(__DIR__ . '/../../../editor_advanced_image.ckeditor5.yml');
    $static_plugin_config = $ckeditor5_plugin_definitions['ckeditor5_editorAdvancedImage']['ckeditor5']['config'];

    $plugin = new EditorAdvancedImage($configuration, 'ckeditor5_editorAdvancedImage', NULL);
    $editor_mock = $this->createMock(EditorInterface::class);
    $dynamic_plugin_config = $plugin->getDynamicPluginConfig($static_plugin_config, $editor_mock);

    $this->assertSame($expected_dynamic_config, $dynamic_plugin_config);
  }

  /**
   * Provides a list of configs to test providerGetDynamicPluginConfig.
   */
  public function providerGetDynamicPluginConfig(): array {
    return [
      'Default configuration' => [
        EditorAdvancedImage::DEFAULT_CONFIGURATION,
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => ''],
            'allowedAttributes' => ['class'],
          ],
        ],
      ],
      'Without configuration' => [
        [],
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => ''],
            'allowedAttributes' => ['class'],
          ],
        ],
      ],
      'Empty configuration' => [
        [
          'default_class' => '',
          'enabled_attributes' => [],
        ],
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => ''],
            'allowedAttributes' => [],
          ],
        ],
      ],
      'With default class' => [
        [
          'default_class' => 'foobar',
          'enabled_attributes' => [],
        ],
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => 'foobar'],
            'allowedAttributes' => [],
          ],
        ],
      ],
      'Attribute class only' => [
        [
          'enabled_attributes' => [
            'class',
          ],
        ],
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => ''],
            'allowedAttributes' => ['class'],
          ],
        ],
      ],
      'Attribute class with default class' => [
        [
          'default_class' => 'foobar',
          'enabled_attributes' => [
            'class',
          ],
        ],
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => 'foobar'],
            'allowedAttributes' => ['class'],
          ],
        ],
      ],
      'Attribute class and title only' => [
        [
          'default_class' => '',
          'enabled_attributes' => [
            'class',
            'title',
          ],
        ],
        [
          'image' => [
            'toolbar' => [
              '|',
              'editorAdvancedImageButton',
            ],
          ],
          'editorAdvancedImageOptions' => [
            'defaults' => ['class' => ''],
            'allowedAttributes' => ['class', 'title'],
          ],
        ],
      ],
    ];
  }

  /**
   * @covers ::validChoices
   */
  public function testValidChoices(): void {
    $this->assertSame(['title', 'class', 'id'], EditorAdvancedImage::validChoices());
  }

  /**
   * @covers ::getElementsSubset
   *
   * @dataProvider providerGetElementsSubset
   */
  public function testGetElementsSubset(array $configuration, array $expected_subset): void {
    $plugin = new EditorAdvancedImage($configuration, 'ckeditor5_editorAdvancedImage', NULL);
    $this->assertSame($expected_subset, $plugin->getElementsSubset());
  }

  /**
   * Provides a list of configs to test getElementsSubset.
   */
  public function providerGetElementsSubset(): iterable {
    return [
      'Default configuration' => [
        EditorAdvancedImage::DEFAULT_CONFIGURATION,
        [
          '<img class>',
        ],
      ],
      'Without configuration' => [
        [],
        [
          '<img class>',
        ],
      ],
      'Empty configuration' => [
        [
          'default_class' => '',
          'enabled_attributes' => [],
        ],
        [],
      ],
      'With default class' => [
        [
          'default_class' => 'foobar',
          'enabled_attributes' => [],
        ],
        [],
      ],
      'Attribute class only' => [
        [
          'enabled_attributes' => [
            'class',
          ],
        ],
        [
          '<img class>',
        ],
      ],
      'Attribute class with default class' => [
        [
          'default_class' => 'foobar',
          'enabled_attributes' => [
            'class',
          ],
        ],
        [
          '<img class>',
        ],
      ],
      'Attribute class and title only' => [
        [
          'default_class' => '',
          'enabled_attributes' => [
            'class',
            'title',
          ],
        ],
        [
          '<img class>',
          '<img title>',
        ],
      ],
    ];
  }

  /**
   * @covers ::getAllowedStringForSupportedAttribute
   *
   * @dataProvider providerGetAllowedStringForSupportedAttribute
   */
  public function testGetAllowedStringForSupportedAttribute(string $attribute, ?string $expected_allowed_attr, ?string $expected_exception_class = NULL): void {
    if ($expected_exception_class !== NULL) {
      $this->expectException($expected_exception_class);
    }
    $allowed_attr = EditorAdvancedImage::getAllowedStringForSupportedAttribute($attribute);
    $this->assertSame($expected_allowed_attr, $allowed_attr);
  }

  /**
   * Provides a list of configs to test getAllowedStringForSupportedAttribute.
   */
  public function providerGetAllowedStringForSupportedAttribute(): iterable {
    yield ['', NULL, \OutOfBoundsException::class];
    yield ['foo', NULL, \OutOfBoundsException::class];
    yield ['foo bar', NULL, \OutOfBoundsException::class];
    yield ['class', 'class'];
    yield ['class foo', NULL, \OutOfBoundsException::class];
    yield ['title', 'title'];
    yield ['id', 'id'];
    yield ['src', NULL, \OutOfBoundsException::class];
  }

  /**
   * @covers ::getAllowedHtmlForSupportedAttribute
   *
   * @dataProvider providerGetAllowedHtmlForSupportedAttribute
   */
  public function testGetAllowedHtmlForSupportedAttribute(string $attribute, ?string $expected_allowed_html, ?string $expected_exception_class = NULL): void {
    if ($expected_exception_class !== NULL) {
      $this->expectException($expected_exception_class);
    }
    $allowed_html = EditorAdvancedImage::getAllowedHtmlForSupportedAttribute($attribute);
    $this->assertSame($expected_allowed_html, $allowed_html);
  }

  /**
   * Provides a list of configs to test getAllowedHtmlForSupportedAttribute.
   */
  public function providerGetAllowedHtmlForSupportedAttribute(): iterable {
    yield ['', NULL, \OutOfBoundsException::class];
    yield ['foo', NULL, \OutOfBoundsException::class];
    yield ['foo bar', NULL, \OutOfBoundsException::class];
    yield ['class', '<img class>'];
    yield ['class bar', NULL, \OutOfBoundsException::class];
    yield ['title', '<img title>'];
    yield ['id', '<img id>'];
    yield ['src', NULL, \OutOfBoundsException::class];
  }

}
