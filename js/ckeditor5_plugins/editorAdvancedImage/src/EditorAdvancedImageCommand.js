/**
 * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImageCommand
 */

import { Command } from "ckeditor5/src/core";

/**
 * The Editor Advanced Image Command plugin
 *
 * @extends module:core/command~Command
 *
 * @private
 */
export default class EditorAdvancedImageCommand extends Command {
  /**
   * Constructs a new object.
   *
   * @param {module:core/editor/editor~Editor} editor
   *   The editor instance.
   * @param {Object<string>} options
   *   All available Drupal Editor Advanced Image options.
   */
  constructor(editor, options) {
    super(editor);
    this.options = options;
  }

  /**
   * @inheritdoc
   *
   * Will be used on every element to store current attributes values.
   */
  refresh() {
    const editor = this.editor;
    const imageUtils = editor.plugins.get("ImageUtils");
    const element = imageUtils.getClosestSelectedImageElement(
      this.editor.model.document.selection
    );

    this.isEnabled = !!element;

    this.attributes = {
      title: false,
      id: false,
      class: false
    };

    // Don't store any values
    if (!this.isEnabled) {
      return;
    }

    // Set the default class configured on the Drupal Editor Plugin.
    if (
      this.options !== undefined &&
      this.options.defaults.class !== undefined
    ) {
      this.attributes.class = this.options.defaults.class;
    }

    // Store the title attribute value when on an image element.
    if (element.hasAttribute("title")) {
      this.attributes.title = element.getAttribute("title");
    }

    // Store the class attribute value when on an image element.
    if (element.hasAttribute("class")) {
      this.attributes.class = element.getAttribute("class");
    }

    // Store the id attribute value when on an image element.
    if (element.hasAttribute("id")) {
      this.attributes.id = element.getAttribute("id");
    }

    // Force an execution at refresh time in order to set attributes even when the Balloon form has still not been used.
    this.execute(this.attributes);
  }

  /**
   * Set attributes to the element.
   */
  execute(attributes) {
    const editor = this.editor;
    const imageUtils = editor.plugins.get("ImageUtils");
    const model = editor.model;
    const imageElement = imageUtils.getClosestSelectedImageElement(
      model.document.selection
    );

    if (attributes.title) {
      model.change(writer =>
        writer.setAttribute("title", attributes.title, imageElement)
      );
    }

    if (attributes.id) {
      model.change(writer =>
        writer.setAttribute("id", attributes.id, imageElement)
      );
    }

    if (attributes.class) {
      model.change(writer =>
        writer.setAttribute("class", attributes.class, imageElement)
      );
    }
  }
}
