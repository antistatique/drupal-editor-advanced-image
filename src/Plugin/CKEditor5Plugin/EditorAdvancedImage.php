<?php

namespace Drupal\editor_advanced_image\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginElementsSubsetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Editor Advanced Image plugin.
 *
 * @internal
 *   Plugin classes are internal.
 */
class EditorAdvancedImage extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface, CKEditor5PluginElementsSubsetInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * The default configuration for this plugin.
   *
   * @var string[][]
   */
  const DEFAULT_CONFIGURATION = [
    'disable_balloon' => FALSE,
    'default_class' => '',
    'enabled_attributes' => [
      'class',
    ],
  ];

  /**
   * All <img> attributes that this plugin supports.
   *
   * @var array
   */
  const SUPPORTED_ATTRIBUTES = [
    'title' => TRUE,
    'class' => TRUE,
    'id' => TRUE,
  ];

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return static::DEFAULT_CONFIGURATION;
  }

  /**
   * Gets all valid choices for the "enabled_attributes" setting.
   *
   * @see editor_advanced_image.schema.yml
   *
   * @return string[]
   *   All valid choices.
   */
  public static function validChoices(): array {
    return array_keys(self::SUPPORTED_ATTRIBUTES);
  }

  /**
   * {@inheritdoc}
   *
   * Form for choosing default class attribute of <img> will be populated.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['disable_balloon'] = [
      '#type' => 'checkbox',
      '#default_value' => $this->configuration['disable_balloon'] ?? self::DEFAULT_CONFIGURATION['disable_balloon'],
      '#title' => $this->t('Disable Balloon'),
      '#description' => $this->t('When checked the plugin will no more display the CKEditor 5 Balloon/Form button on Image element.'),
    ];

    $form['enabled_attributes'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Enabled attributes'),
      '#description' => $this->t('These are the attributes that will appear when creating or editing images.'),
    ];

    // UI labels corresponding to each of the supported attributes.
    $config_ui_labels = [
      'title' => $this->t('Title'),
      'class' => $this->t('CSS classes'),
      'id' => $this->t('ID'),
    ];
    assert(count(self::SUPPORTED_ATTRIBUTES) === count($config_ui_labels));

    foreach (array_keys(self::SUPPORTED_ATTRIBUTES) as $attribute) {
      $form['enabled_attributes'][$attribute] = [
        '#type' => 'checkbox',
        '#title' => $this->t('@label (<code>@attribute</code>)', [
          '@label' => $config_ui_labels[$attribute],
          '@attribute' => self::getAllowedStringForSupportedAttribute($attribute),
        ]),
        '#return_value' => $attribute,
      ];
      $form['enabled_attributes'][$attribute]['#default_value'] = in_array($attribute, $this->configuration['enabled_attributes'], TRUE);
    }

    $form['default_class'] = [
      '#title' => $this->t('Default image class(es)'),
      '#type' => 'textfield',
      '#default_value' => !empty($this->configuration['default_class']) ? $this->configuration['default_class'] : '',
      '#description' => $this->t('A list of classes that will be added when the user adds an inline-image with CKEditor. <br>Enter one or more classes separated by spaces. Example: <code>img-responsive</code> or <code>img-fluid</code>.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Match the config schema structure at
    // ckeditor5.plugin.editor_advanced_image_image.
    $form_state->setValue('default_class', $form_state->getValue('default_class'));

    $form_value = $form_state->getValue('enabled_attributes');
    $config_value = array_values(array_filter($form_value));
    $form_state->setValue('enabled_attributes', $config_value);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['default_class'] = $form_state->getValue('default_class');
    $this->configuration['enabled_attributes'] = $form_state->getValue('enabled_attributes');
    $this->configuration['disable_balloon'] = (bool) $form_state->getValue('disable_balloon');
  }

  /**
   * {@inheritdoc}
   */
  public function getElementsSubset(): array {
    $subset = [];
    foreach ($this->configuration['enabled_attributes'] as $attribute) {
      $subset[] = self::getAllowedHtmlForSupportedAttribute($attribute);
    }
    return $subset;
  }

  /**
   * Gets the allowed string representation for a supported attribute.
   *
   * @param string $attribute
   *   One of self::SUPPORTED_ATTRIBUTES.
   *
   * @return string
   *   The corresponding allowed string representation.
   */
  public static function getAllowedStringForSupportedAttribute(string $attribute): string {
    if (!array_key_exists($attribute, self::SUPPORTED_ATTRIBUTES)) {
      throw new \OutOfBoundsException();
    }

    $allowed_values = self::SUPPORTED_ATTRIBUTES[$attribute];
    if ($allowed_values === TRUE) {
      // For attributes for which any value can be created.
      return $attribute;
    }
  }

  /**
   * Gets the allowed HTML string representation for a supported attribute.
   *
   * @param string $attribute
   *   One of self::SUPPORTED_ATTRIBUTES.
   *
   * @return string
   *   The corresponding allowed HTML string representation.
   */
  public static function getAllowedHtmlForSupportedAttribute(string $attribute): string {
    if (!array_key_exists($attribute, self::SUPPORTED_ATTRIBUTES)) {
      throw new \OutOfBoundsException();
    }

    $allowed_values = self::SUPPORTED_ATTRIBUTES[$attribute];
    if ($allowed_values === TRUE) {
      // For attributes for which any value can be created.
      return sprintf('<img %s>', $attribute);
    }
  }

  /**
   * Filters the default options configured in editor config.
   *
   * This is the options that will be available to the CKEditor 5 Javascript.
   *
   * @see https://api.drupal.org/api/drupal/core%21modules%21ckeditor5%21ckeditor5.api.php/function/hook_ckeditor5_plugin_info_alter
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $allowed_attributes = [];
    foreach ($this->configuration['enabled_attributes'] as $attribute) {
      $allowed_attributes[] = self::getAllowedStringForSupportedAttribute($attribute);
    }

    return [
      'image' => [
        'toolbar' => [
          '|',
          'editorAdvancedImageButton',
        ],
      ],
      'editorAdvancedImageOptions' => [
        'disable_balloon' => (bool) $this->configuration['disable_balloon'],
        'defaults' => ['class' => $this->configuration['default_class']],
        'allowedAttributes' => $allowed_attributes,
      ],
    ];
  }

}
