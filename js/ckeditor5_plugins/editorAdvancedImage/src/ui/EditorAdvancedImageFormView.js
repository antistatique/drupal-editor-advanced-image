/**
 * @module editor_advanced_image/editorAdvancedImage/ui/EditorAdvancedImageFormView
 */

import {
  ButtonView,
  LabeledFieldView,
  LabelView,
  View,
  createLabeledInputText
} from "ckeditor5/src/ui";
import { KeystrokeHandler } from "ckeditor5/src/utils";
import { icons } from "ckeditor5/src/core";

/**
 * A class rendering Editor Advanced Image form view.
 *
 * @extends module:ui/view~View
 *
 * @internal
 */
export default class EditorAdvancedImageFormView extends View {
  /**
   * @inheritdoc
   */
  constructor(locale, options) {
    super(locale);

    this.options = options;

    /**
     * An instance of the {@link module:utils/keystrokehandler~KeystrokeHandler}.
     *
     * @readonly
     * @member {module:utils/keystrokehandler~KeystrokeHandler}
     */
    this.keystrokes = new KeystrokeHandler();

    /**
     * The form title.
     *
     * @member {module:ui/label/labelview~LabelView} #classAttrInput
     */
    this.formTitle = this._createLabelView("Editor Advanced Image");

    /**
     * The Class attribute input.
     *
     * @member {module:ui/labeledfield/labeledfieldview~LabeledFieldView} #classAttrInput
     */
    this.classAttrInput = this._createLabeledInputView(
      "CSS classes",
      "List of CSS classes to be added to the image, separated by spaces."
    );

    /**
     * The Title attribute input .
     *
     * @member {module:ui/labeledfield/labeledfieldview~LabeledFieldView} #titleAttrInput
     */
    this.titleAttrInput = this._createLabeledInputView(
      "Title",
      "Populates the title attribute of the image, usually shown as a small tooltip on hover."
    );

    /**
     * The ID attribute input .
     *
     * @member {module:ui/labeledfield/labeledfieldview~LabeledFieldView} #idAttrInput
     */
    this.idAttrInput = this._createLabeledInputView(
      "ID",
      "Usually used to linking to this content using a https://en.wikipedia.org/wiki/Fragment_identifier. Must be unique on the page."
    );

    /**
     * A button used to submit the form.
     *
     * @member {module:ui/button/buttonview~ButtonView} #saveButtonView
     */
    this.saveButtonView = this._createButton(
      Drupal.t("Save"),
      icons.check,
      "ck-button-save",
      "save"
    );

    /**
     * A button used to cancel the form.
     *
     * @member {module:ui/button/buttonview~ButtonView} #cancelButtonView
     */
    this.cancelButtonView = this._createButton(
      Drupal.t("Cancel"),
      icons.cancel,
      "ck-button-cancel",
      "cancel"
    );

    // Build the children form items.
    const children = [
      {
        tag: "div",
        attributes: {
          class: ["ck"]
        },
        children: [this.formTitle]
      }
    ];

    // Add each inputs only if the attribute is allowed.
    if (this.options.allowedAttributes.includes("class")) {
      children.push(this.classAttrInput);
    }

    // Add each inputs only if the attribute is allowed.
    if (this.options.allowedAttributes.includes("title")) {
      children.push(this.titleAttrInput);
    }

    // Add each inputs only if the attribute is allowed.
    if (this.options.allowedAttributes.includes("id")) {
      children.push(this.idAttrInput);
    }

    children.push(this.saveButtonView);
    children.push(this.cancelButtonView);

    this.setTemplate({
      tag: "form",

      attributes: {
        class: [
          "ck",
          "ck-vertical-form",
          "ck-editor-advanced-image",
          "ck-responsive-form"
        ],

        // https://github.com/ckeditor/ckeditor5-image/issues/40
        tabindex: "-1"
      },

      children
    });

    // injectCssTransitionDisabler(this);
  }

  /**
   * @inheritdoc
   */
  destroy() {
    super.destroy();

    this.keystrokes.destroy();
  }

  /**
   * Creates a label.
   *
   * @returns {module:ui/label/labelview~LabelView}
   *   Labeled view instance.
   *
   * @private
   */
  _createLabelView(text) {
    const label = new LabelView(this.locale);
    label.text = Drupal.t(text);
    return label;
  }

  /**
   * Creates the button view.
   *
   * @param {String} label
   *   The button label
   * @param {String} icon
   *   The button's icon.
   * @param {String} className
   *   The additional button CSS class name.
   * @param {String} [eventName]
   *   The event name that the ButtonView#execute event will be delegated to.
   * @returns {module:ui/button/buttonview~ButtonView}
   *   The button view instance.
   *
   * @private
   */
  _createButton(label, icon, className, eventName) {
    const button = new ButtonView(this.locale);

    button.set({
      label,
      icon,
      tooltip: true
    });

    button.extendTemplate({
      attributes: {
        class: className
      }
    });

    if (eventName) {
      button.delegate("execute").to(this, eventName);
    }

    return button;
  }

  /**
   * Creates an input with a label.
   *
   * @returns {module:ui/labeledfield/labeledfieldview~LabeledFieldView}
   *   Labeled field view instance.
   *
   * @private
   */
  _createLabeledInputView(label, infoText) {
    const labeledInput = new LabeledFieldView(
      this.locale,
      createLabeledInputText
    );

    labeledInput.label = Drupal.t(label);
    labeledInput.infoText = Drupal.t(infoText);

    return labeledInput;
  }
}
