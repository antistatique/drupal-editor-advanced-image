<?php

namespace Drupal\editor_advanced_image\Plugin\CKEditorPlugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\ckeditor\CKEditorPluginContextualInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * CKEditor 4 Editor Advanced Image plugin.
 *
 * @CKEditorPlugin(
 *   id = "editoradvancedimage",
 *   label = @Translation("Editor Advanced Image"),
 *   module = "ckeditor"
 * )
 */
class EditorAdvancedImage extends PluginBase implements ContainerFactoryPluginInterface, CKEditorPluginInterface, CKEditorPluginContextualInterface, CKEditorPluginConfigurableInterface {

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected ModuleExtensionList $moduleExtensionList;

  /**
   * Constructs a new Editor Advanced Image plugin object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extensionListModule
   *   The module extension list.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ModuleExtensionList $extensionListModule) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleExtensionList = $extensionListModule;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('extension.list.module')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'ckeditor/drupal.ckeditor.plugins.editoradvancedimage',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->moduleExtensionList->getPath('editor_advanced_image') . '/js/editor_advanced_image.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $config = [];
    $settings = $editor->getSettings();

    if (!isset($settings['plugins']['editoradvancedimage']['default_class'])) {
      return $config;
    }

    $config['defaultClasses'] = $settings['plugins']['editoradvancedimage']['default_class'];
    return $config;
  }

  /**
   * {@inheritdoc}
   *
   * @see \Drupal\editor\Form\EditorImageDialog
   * @see editor_image_upload_settings_form()
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    // Defaults.
    $settings = $editor->getSettings();

    $form['default_class'] = [
      '#title'         => $this->t('Default image class(es)'),
      '#type'          => 'textfield',
      '#default_value' => !empty($settings['plugins']['editoradvancedimage']['default_class']) ? $settings['plugins']['editoradvancedimage']['default_class'] : '',
      '#description' => $this->t('A list of classes that will be added when the user adds an inline-image with CKEditor. <br>Enter one or more classes separated by spaces. Example: <code>img-responsive</code> or <code>img-fluid</code>.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(Editor $editor) {
    // Check if a DrupalImage has been placed in the CKeditor.
    $settings = $editor->getSettings();
    if ($this->checkImageEnable($settings['toolbar']['rows'][0])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if a DrupalImage exists in the given toolbar row.
   *
   * @param array $toolbar
   *   A CKeditor toolbar row containing Ckeditor plugin items.
   *
   * @return bool
   *   Does the DrupalImage has been placed in the CKeditor.
   */
  public function checkImageEnable(array $toolbar) {
    foreach ($toolbar as $items) {
      foreach ($items['items'] as $item) {
        if ('DrupalImage' === $item) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }

}
