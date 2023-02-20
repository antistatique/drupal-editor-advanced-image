/**
 * @module editor_advanced_image/editorAdvancedImage/ui/EditorAdvancedImageFormView
 */

import {
  ButtonView,
  LabeledFieldView,
  LabelView,
  View,
  createLabeledInputText,
  FormHeaderView
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
     * A collection of child views in the form.
     *
     * @readonly
     * @type {module:ui/viewcollection~ViewCollection}
     */
    this.children = this.createCollection();

    /**
     * An instance of the {@link module:utils/keystrokehandler~KeystrokeHandler}.
     *
     * @readonly
     * @member {module:utils/keystrokehandler~KeystrokeHandler}
     */
    this.keystrokes = new KeystrokeHandler();

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

    // Form header.
    this.children.add(
      new FormHeaderView(locale, {
        label: this.t("Editor Advanced Image")
      })
    );

    // Properties Form Panel (ID, Class, Title) Input(s) row.
    // ------------------------------------------------
    this.children.add(
      this._createRowView([this._createLabelView("Properties")])
    );

    // Add each inputs only if the attribute is allowed.
    // The "class" input.
    if (this.options.allowedAttributes.includes("class")) {
      this.children.add(this._createRowView([this.classAttrInput]));
    }

    // The "title" input.
    if (this.options.allowedAttributes.includes("title")) {
      this.children.add(this._createRowView([this.titleAttrInput]));
    }

    // The "id" input.
    if (this.options.allowedAttributes.includes("id")) {
      this.children.add(this._createRowView([this.idAttrInput]));
    }

    // Actions (Save & Cancel). row.
    // ------------------------------------------------
    this.children.add(
      this._createRowView(
        [this.saveButtonView, this.cancelButtonView],
        ["ck-table-form__action-row"]
      )
    );

    this.setTemplate({
      tag: "form",
      attributes: {
        class: ["ck", "ck-form", "ck-editor-advanced-image"],
        // https://github.com/ckeditor/ckeditor5-link/issues/90 & https://github.com/ckeditor/ckeditor5-image/issues/40
        tabindex: "-1"
      },
      children: this.children
    });
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
      withText: true
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

  /**
   * Creates a complex Row View.
   *
   * @returns {module:ui/view~View}
   *   The Row view.
   *
   * @private
   */
  _createRowView(children, classes) {
    const view = new View();
    view.setTemplate({
      tag: "div",
      attributes: {
        class: ["ck", "ck-form__row", classes !== undefined ? classes : ""]
      },
      children
    });

    return view;
  }
}
