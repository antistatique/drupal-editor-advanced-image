/**
 * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImageUI
 */

import { Plugin, icons } from "ckeditor5/src/core";
import {
  ButtonView,
  ContextualBalloon,
  clickOutsideHandler
} from "ckeditor5/src/ui";

import { getBalloonPositionData } from "@ckeditor/ckeditor5-image/src/image/ui/utils";

import EditorAdvancedImageFormView from "./ui/EditorAdvancedImageFormView";

/**
 * The Editor Advanced Image UI plugin.
 *
 * The plugin uses the contextual balloon.
 *
 * @see module:ui/panel/balloon/contextualballoon~ContextualBalloon
 *
 * @extends module:core/plugin~Plugin
 *
 * @internal
 */
export default class EditorAdvancedImageUI extends Plugin {
  /**
   * @inheritdoc
   */
  static get requires() {
    return [ContextualBalloon];
  }

  /**
   * @inheritdoc
   */
  init() {
    const options = this.editor.config.get("editorAdvancedImageOptions");

    // Prevent creation of Editor Advanced Button & Form when the option "Disable Balloon" is enabled.
    if (
      options !== undefined &&
      options.disable_balloon !== undefined &&
      options.disable_balloon
    ) {
      return;
    }

    this._createButton();
    this._createForm();
  }

  /**
   * @inheritdoc
   */
  destroy() {
    super.destroy();

    // Destroy created UI components as they are not automatically destroyed
    // @see https://github.com/ckeditor/ckeditor5/issues/1341
    this._form.destroy();
  }

  /**
   * Creates a button showing the balloon panel for changing the image advanced
   * attributes alternative and registers it in the editor component factory.
   *
   * @see module:ui/componentfactory~ComponentFactory
   *
   * @private
   */
  _createButton() {
    const editor = this.editor;

    // The name on the component factory must be the same as in editor_advanced_image.ckeditor5.yml.
    editor.ui.componentFactory.add("editorAdvancedImageButton", locale => {
      const command = editor.commands.get("EditorAdvancedImageCmd");
      const view = new ButtonView(locale);

      view.set({
        label: Drupal.t("Editor Advanced Image"),
        icon: icons.threeVerticalDots,
        tooltip: true
      });

      // Enable the UI if and only if the command is enabled.
      view.bind("isVisible").to(command, "isEnabled");

      this.listenTo(view, "execute", () => {
        this._showForm();
      });

      return view;
    });
  }

  /**
   * Creates the {@link module:editor_advanced_image/editorAdvancedImage/ui/EditorAdvancedImageFormView~EditorAdvancedImageFormView}
   * form.
   *
   * @private
   */
  _createForm() {
    const editor = this.editor;

    // Get the Drupal Configurations.
    // @see \Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage::getDynamicPluginConfig
    const options = editor.config.get(`editorAdvancedImageOptions`);

    /**
     * The contextual balloon plugin instance.
     */
    this._balloon = this.editor.plugins.get("ContextualBalloon");

    /**
     * A form containing class, title & id inputs, used to change data-attributes values.
     */
    this._form = new EditorAdvancedImageFormView(editor.locale, options);

    // Render the form so its #element is available for clickOutsideHandler.
    this._form.render();

    // When the form is submitted, close the form instead of POST the whole page.
    this.listenTo(this._form, "save", () => {
      // Execute the Command.
      editor.execute("EditorAdvancedImageCmd", {
        title: options.allowedAttributes.includes("title")
          ? this._form.titleAttrInput.fieldView.element.value
          : false,
        class: options.allowedAttributes.includes("class")
          ? this._form.classAttrInput.fieldView.element.value
          : false,
        id: options.allowedAttributes.includes("id")
          ? this._form.idAttrInput.fieldView.element.value
          : false
      });

      this._hideForm(true);
    });

    // Close the form when clicking on the cancel button.
    this.listenTo(this._form, "cancel", () => {
      this._hideForm(true);
    });

    // Close the form on Esc key press.
    this._form.keystrokes.set("Esc", (data, cancel) => {
      this._hideForm(true);
      cancel();
    });

    // Close on click outside of balloon panel element.
    clickOutsideHandler({
      emitter: this._form,
      activator: () => this._isVisible,
      contextElements: [this._balloon.view.element],
      callback: () => this._hideForm()
    });
  }

  /**
   * Shows the form in a balloon.
   */
  _showForm() {
    if (this._isVisible) {
      return;
    }

    const editor = this.editor;
    const command = editor.commands.get("EditorAdvancedImageCmd");

    // Get the Drupal Configurations.
    // @see \Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage::getDynamicPluginConfig
    const options = editor.config.get(`editorAdvancedImageOptions`);

    // this._form.disableCssTransitions();

    if (!this._isInBalloon) {
      // Place the form into the Balloon.
      this._balloon.add({
        view: this._form,
        // Be sure to keep the Balloon at the same centered position.
        position: getBalloonPositionData(editor)
      });
    }

    // Make sure that each time the panel shows up, the field remains in sync with the value of
    // the command. If the user typed in the input, then canceled the balloon (`labeledInput#value`
    // stays unaltered) and re-opened it without changing the value of the command, they would see the
    // old value instead of the actual value of the command.
    // https://github.com/ckeditor/ckeditor5-image/issues/114
    if (options.allowedAttributes.includes("title")) {
      this._form.titleAttrInput.fieldView.element.value =
        command.attributes.title || "";
      this._form.titleAttrInput.fieldView.value = this._form.titleAttrInput.fieldView.element.value;
    }

    if (options.allowedAttributes.includes("class")) {
      this._form.classAttrInput.fieldView.element.value =
        command.attributes.class || "";
      this._form.classAttrInput.fieldView.value = this._form.classAttrInput.fieldView.element.value;
    }

    if (options.allowedAttributes.includes("id")) {
      this._form.idAttrInput.fieldView.element.value =
        command.attributes.id || "";
      this._form.idAttrInput.fieldView.value = this._form.idAttrInput.fieldView.element.value;
    }
    // this._form.enableCssTransitions();
  }

  /**
   * Removes the {@link #_form} from the {@link #_balloon}.
   *
   * @param {Boolean} [focusEditable=false] Controls whether the editing view is focused afterwards.
   * @private
   */
  _hideForm(focusEditable) {
    if (!this._isInBalloon) {
      return;
    }

    this._balloon.remove(this._form);

    if (focusEditable) {
      this.editor.editing.view.focus();
    }
  }

  /**
   * Returns `true` when the form is the visible view in the balloon.
   *
   * @type {Boolean}
   */
  get _isVisible() {
    return this._balloon.visibleView === this._form;
  }

  /**
   * Returns `true` when the form is in the balloon.
   *
   * @type {Boolean}
   */
  get _isInBalloon() {
    return this._balloon.hasView(this._form);
  }
}
