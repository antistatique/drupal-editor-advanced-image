<?php

declare(strict_types = 1);

namespace Drupal\Tests\editor_advanced_image\Kernel\CKEditor4To5Upgrade;

use Drupal\editor\Entity\Editor;
use Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\ckeditor5\Kernel\SmartDefaultSettingsTest;

/**
 * @covers \Drupal\editor_advanced_image\Plugin\CKEditor4To5Upgrade\EditorAdvancedImage
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_kernel
 * @group editor_advanced_image_ckeditor5
 * @group ckeditor5
 *
 * @internal
 *
 * @requires module ckeditor5
 */
class UpgradePathTest extends SmartDefaultSettingsTest {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'editor_advanced_image',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    if (version_compare(\Drupal::VERSION, '10', '<')) {
      $this->markTestSkipped('Upgrade path will only run properly on Drupal 10+ because of config sorting.');
    }

    // Create test FilterFormat config entities: one per option to test in
    // isolation, plus one to test with the default configuration (class attr
    // enabled), plus one with ALL attributes enabled but with additional
    // attributes not supported by EditorAdvancedImage.
    $get_filter_config = function (string $img_allowed_html_addition): array {
      return [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <img src alt height width ' . $img_allowed_html_addition . '>',
          ],
        ],
      ];
    };

    // Build filter format, one per enabled attribute (option) to test in
    // isolation.
    $options = EditorAdvancedImage::SUPPORTED_ATTRIBUTES;
    foreach (array_keys($options) as $option) {
      $string_representation = EditorAdvancedImage::getAllowedHtmlForSupportedAttribute($option);
      FilterFormat::create([
        'format' => "editor_advanced_image__$option",
        'name' => $string_representation,
        'filters' => $get_filter_config(EditorAdvancedImage::getAllowedStringForSupportedAttribute($option)),
      ])->setSyncing(TRUE)->save();
    }
    FilterFormat::create([
      'format' => 'editor_advanced_image__none',
      'name' => 'None, just plain img',
      'filters' => [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <img src alt height width>',
          ],
        ],
      ],
    ])->setSyncing(TRUE)->save();
    FilterFormat::create([
      'format' => 'editor_advanced_image__all_and_more',
      'name' => 'All and more',
      'filters' => [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <img src alt height width foo bar="baz" title class id>',
          ],
        ],
      ],
    ])->setSyncing(TRUE)->save();

    // Create matching Text Editors.
    $cke4_settings = [
      'toolbar' => [
        'rows' => [
          0 => [
            [
              'name' => 'Basic Formatting',
              'items' => [
                'Bold',
                'Format',
                'DrupalImage',
              ],
            ],
          ],
        ],
      ],
      'plugins' => [],
    ];
    foreach (array_keys($options) as $option) {
      Editor::create([
        'format' => "editor_advanced_image__$option",
        'editor' => 'ckeditor',
        'settings' => $cke4_settings,
      ])->setSyncing(TRUE)->save();
    }
    Editor::create([
      'format' => 'editor_advanced_image__none',
      'editor' => 'ckeditor',
      'settings' => $cke4_settings,
    ])->setSyncing(TRUE)->save();

    $cke4_settings['plugins']['editoradvancedimage'] = [
      'default_class' => 'foobar',
    ];
    Editor::create([
      'format' => 'editor_advanced_image__all_and_more',
      'editor' => 'ckeditor',
      'settings' => $cke4_settings,
    ])->setSyncing(TRUE)->save();
  }

  /**
   * {@inheritdoc}
   */
  public function provider() {
    $expected_ckeditor5_toolbar = [
      'items' => [
        'bold',
        'drupalInsertImage',
      ],
    ];

    yield '<img title> + CKEditor 4 DrupalImage' => [
      'format_id' => 'editor_advanced_image__title',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => $expected_ckeditor5_toolbar,
        'plugins' => [
          'ckeditor5_imageResize' => ['allow_resize' => TRUE],
          'editor_advanced_image_image' => [
            'default_class' => '',
            'enabled_attributes' => [
              'title',
            ],
          ],
        ],
      ],
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [],
    ];

    yield '<img class> + CKEditor 4 DrupalImage' => [
      'format_id' => 'editor_advanced_image__class',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => $expected_ckeditor5_toolbar,
        'plugins' => [
          'ckeditor5_imageResize' => ['allow_resize' => TRUE],
          'editor_advanced_image_image' => [
            'default_class' => '',
            'enabled_attributes' => [
              'class',
            ],
          ],
        ],
      ],
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [],
    ];

    yield '<img id> + CKEditor 4 DrupalImage' => [
      'format_id' => 'editor_advanced_image__id',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => $expected_ckeditor5_toolbar,
        'plugins' => [
          'ckeditor5_imageResize' => ['allow_resize' => TRUE],
          'editor_advanced_image_image' => [
            'default_class' => '',
            'enabled_attributes' => [
              'id',
            ],
          ],
        ],
      ],
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [],
    ];

    yield 'None, just plain img + CKEditor 4 DrupalImage' => [
      'format_id' => 'editor_advanced_image__none',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => [
          'items' => [
            'bold',
            'drupalInsertImage',
          ],
        ],
        'plugins' => [
          'ckeditor5_imageResize' => ['allow_resize' => TRUE],
          'editor_advanced_image_image' => EditorAdvancedImage::DEFAULT_CONFIGURATION,
        ],
      ],
      'expected_superset' => '<img class>',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [
        'warning' => [
          'Updating to CKEditor 5 added support for some previously unsupported tags/attributes. A plugin introduced support for the following:   This attribute: <em class="placeholder"> class (for &lt;img&gt;)</em>; Additional details are available in your logs.',
        ],
      ],
    ];

    yield '<img FOO title class id BAR="baz"> + CKEditor 4 DrupalImage' => [
      'format_id' => 'editor_advanced_image__all_and_more',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => [
          'items' => [
            'bold',
            'drupalInsertImage',
            'sourceEditing',
          ],
        ],
        'plugins' => [
          'ckeditor5_imageResize' => ['allow_resize' => TRUE],
          'ckeditor5_sourceEditing' => [
            'allowed_tags' => [
              '<img foo bar="baz">',
            ],
          ],
          'editor_advanced_image_image' => [
            'default_class' => 'foobar',
            'enabled_attributes' => [
              'class',
              'id',
              'title',
            ],
          ],
        ],
      ],
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [
        'status' => [
          'As part of migrating to CKEditor 5, it was found that the <em class="placeholder">All and more</em> text format\'s HTML filters includes plugins that support the following tags, but not some of their attributes. To ensure these attributes remain supported, the following were added to the Source Editing plugin\'s <em>Manually editable HTML tags</em>: &lt;img foo bar=&quot;baz&quot;&gt;. The text format must be saved to make these changes active.',
        ],
      ],
      'expected_messages' => [
        'status' => [
          'To maintain the capabilities of this text format, <a target="_blank" href="/admin/help/ckeditor5#migration-settings">the CKEditor 5 migration</a> did the following:  Added these tags/attributes to the Source Editing Plugin\'s <a target="_blank" href="/admin/help/ckeditor5#source-editing">Manually editable HTML tags</a> setting: &lt;img foo bar=&quot;baz&quot;&gt;. Additional details are available in your logs.',
        ],
      ],
    ];

    // Verify that none of the core test cases are broken; especially important
    // for EditorAdvancedImage since it extends the behavior of Drupal core.
    // @see Drupal\Tests\ckeditor5\Kernel\SmartDefaultSettingsTest
    $formats_not_supporting_img = [
      'cke4_stylescombo_span',
      'filter_only__filter_html',
      'restricted_html',
      'cke4_plugins_with_settings',
      'cke4_contrib_plugins_now_in_core',
      'minimal_ckeditor_wrong_allowed_html',
    ];
    $full_html_configuration = [
      'default_class' => '',
      'enabled_attributes' => array_keys(EditorAdvancedImage::SUPPORTED_ATTRIBUTES),
    ];
    sort($full_html_configuration['enabled_attributes']);

    foreach (parent::provider() as $label => $case) {
      // The `editor_advanced_image_image` plugin settings will appear for every
      // upgraded text editor while editor_advanced_image is installed, as long
      // as it has the `DrupalImage` button enabled in CKEditor 4.
      if (!in_array($case['format_id'], $formats_not_supporting_img, TRUE)) {
        $case['expected_superset'] .= ' <img class>';
        $case['expected_superset'] = trim($case['expected_superset'], ' ');

        // A Warning message will be triggered as <img> was not present on the
        // HTML limited tag.
        $case['expected_messages']['warning'][] = 'Updating to CKEditor 5 added support for some previously unsupported tags/attributes. A plugin introduced support for the following:   This attribute: <em class="placeholder"> class (for &lt;img&gt;)</em>; Additional details are available in your logs.';

        // The previous warning is a bit different on basic_html_with_pre.
        if ($case['format_id'] === 'basic_html_with_pre') {
          $case['expected_messages']['warning'] = ['Updating to CKEditor 5 added support for some previously unsupported tags/attributes. A plugin introduced support for the following:   This attribute: <em class="placeholder"> class (for &lt;code&gt;, &lt;img&gt;)</em>; Additional details are available in your logs.'];
        }

        // The previous warning is a bit different on
        // basic_html_with_alignable_p.
        if ($case['format_id'] === 'basic_html_with_alignable_p') {
          $case['expected_messages']['warning'] = ['Updating to CKEditor 5 added support for some previously unsupported tags/attributes. A plugin introduced support for the following:   This attribute: <em class="placeholder"> class (for &lt;h2&gt;, &lt;h3&gt;, &lt;h4&gt;, &lt;h5&gt;, &lt;h6&gt;, &lt;img&gt;)</em>; Additional details are available in your logs.'];
        }

        // Add the default Editor Advanced Image configuration excepted for
        // full_html that must enable every Editor Advanced Image options.
        $case['expected_ckeditor5_settings']['plugins']['editor_advanced_image_image'] = EditorAdvancedImage::DEFAULT_CONFIGURATION;
        if ($case['format_id'] === 'full_html') {
          $case['expected_ckeditor5_settings']['plugins']['editor_advanced_image_image'] = $full_html_configuration;
          unset($case['expected_messages']['warning']);
        }

        // Reorder the plugins settings as we manually added Editor Advanced
        // Image one.
        ksort($case['expected_ckeditor5_settings']['plugins']);
      }

      yield $label => $case;
    }
  }

}
