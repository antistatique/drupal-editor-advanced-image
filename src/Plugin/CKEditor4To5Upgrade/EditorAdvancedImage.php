<?php

declare(strict_types=1);

namespace Drupal\editor_advanced_image\Plugin\CKEditor4To5Upgrade;

use Drupal\ckeditor5\HTMLRestrictions;
use Drupal\ckeditor5\Plugin\CKEditor4To5UpgradePluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\filter\FilterFormatInterface;
use Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage as CKEditor5Plugin;

/**
 * Provides the CKEditor 4 to 5 upgrade for Editor Advanced Image.
 *
 * @CKEditor4To5Upgrade(
 *   id = "editor_advanced_image",
 *   cke4_buttons = {},
 *   cke4_plugin_settings = {
 *     "editoradvancedimage",
 *   },
 *   cke5_plugin_elements_subset_configuration = {
 *     "editor_advanced_image_image",
 *   }
 * )
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class EditorAdvancedImage extends PluginBase implements CKEditor4To5UpgradePluginInterface {

  /**
   * {@inheritdoc}
   *
   * phpcs:disable Drupal.NamingConventions.ValidFunctionName
   */
  public function mapCKEditor4ToolbarButtonToCKEditor5ToolbarItem(string $cke4_button, HTMLRestrictions $text_format_html_restrictions): ?array {
    throw new \OutOfBoundsException();
  }

  /**
   * {@inheritdoc}
   *
   * phpcs:disable Drupal.NamingConventions.ValidFunctionName
   */
  public function mapCKEditor4SettingsToCKEditor5Configuration(string $cke4_plugin_id, array $cke4_plugin_settings): ?array {
    switch ($cke4_plugin_id) {
      // @see \Drupal\ckeditor\Plugin\CKEditorPlugin\StylesCombo
      case 'editoradvancedimage':
        $default_class = '';
        if (isset($cke4_plugin_settings['default_class'])) {
          $default_class = $cke4_plugin_settings['default_class'];
        }

        return [
          'editor_advanced_image_image' => [
            'disable_balloon' => FALSE,
            'default_class' => $default_class,
            'enabled_attributes' => [],
          ],
        ];

      default:
        throw new \OutOfBoundsException();
    }
  }

  /**
   * {@inheritdoc}
   *
   * phpcs:disable Drupal.NamingConventions.ValidFunctionName
   */
  public function computeCKEditor5PluginSubsetConfiguration(string $cke5_plugin_id, FilterFormatInterface $text_format): ?array {
    switch ($cke5_plugin_id) {
      case 'editor_advanced_image_image':
      default:
        $configuration = [];

        $restrictions = $text_format->getHtmlRestrictions();
        if ($restrictions === FALSE) {
          // When no restrictions are given, then enable all the options.
          return ['enabled_attributes' => array_keys(CKEditor5Plugin::SUPPORTED_ATTRIBUTES)];
        }

        // Otherwise, only enable attributes that allowed by the restrictions.
        foreach (array_keys(CKEditor5Plugin::SUPPORTED_ATTRIBUTES) as $attribute) {
          $img_allowed_attributes = $restrictions['allowed']['img'] ?: [];
          // Check whether the attribute is allowed.
          // @see \Drupal\filter\Plugin\FilterInterface::getHTMLRestrictions()
          if (array_key_exists($attribute, $img_allowed_attributes) && $img_allowed_attributes[$attribute] === TRUE) {
            $configuration['enabled_attributes'][] = $attribute;
          }
        }
        return $configuration;
    }
  }

}
