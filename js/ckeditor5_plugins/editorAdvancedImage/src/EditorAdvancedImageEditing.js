/**
 * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImageCommand
 */

import { Plugin } from "ckeditor5/src/core";
import EditorAdvancedImageCommand from "./EditorAdvancedImageCommand";

/**
 * The Editor Advanced Image Editing plugin.
 *
 * Registers the `EditorAdvancedImage` command.
 *
 * @extends module:core/plugin~Plugin
 *
 * @internal
 */
export default class EditorAdvancedImageEditing extends Plugin {
  /**
   * @inheritdoc
   */
  static get requires() {
    return ["ImageUtils"];
  }

  /**
   * @inheritdoc
   */
  static get pluginName() {
    return "EditorAdvancedImageEditing";
  }

  init() {
    const editor = this.editor;

    // Get the Drupal Configurations.
    // @see \Drupal\editor_advanced_image\Plugin\CKEditor5Plugin\EditorAdvancedImage::getDynamicPluginConfig
    const options = editor.config.get(`editorAdvancedImageOptions`);

    this._defineSchema();
    this._defineConverters();

    // Declare our command.
    editor.commands.add(
      "EditorAdvancedImageCmd",
      new EditorAdvancedImageCommand(editor, options)
    );
  }

  /**
   * Define Schema with allowed attributes that will automatically be used by
   * Drupal "	Limit allowed HTML tags and correct faulty HTML".
   */
  _defineSchema() {
    const { editor } = this;
    const { schema } = editor.model;

    if (schema.isRegistered("imageInline")) {
      schema.extend("imageInline", {
        allowAttributes: ["title", "class", "id"]
      });
    }

    if (schema.isRegistered("imageBlock")) {
      schema.extend("imageBlock", {
        allowAttributes: ["title", "class", "id"]
      });
    }
  }

  _defineConverters() {
    const { editor } = this;
    const { conversion } = this.editor;
    const { schema } = editor.model;

    // Upcast Converters: determine how existing HTML is interpreted by the
    // editor. These trigger when an editor instance loads.
    conversion.for("upcast").attributeToAttribute({
      model: "title",
      view: "title"
    });
    conversion.for("upcast").attributeToAttribute({
      model: "id",
      view: "id"
    });
    conversion.for("upcast").attributeToAttribute({
      model: "class",
      view: "class"
    });

    // Data Downcast Converters: converts stored model data into HTML.
    // These trigger when content is saved.
    conversion
      .for("downcast")
      // We can't use 'dataDowncast'.attributeToAttribute as we want to apply attributes on the <img> and
      // not the <figure> element.
      .add(modelAttributeToDataAttribute());
  }
}

/**
 * Generates a callback that saves the attributes value to an attribute on
 * data downcast.
 *
 * @return {function}
 *  Callback that binds an event to it's parameter.
 *
 * @private
 */
function modelAttributeToDataAttribute() {
  /**
   * Callback for the attribute:[title|class|id] event.
   *
   * Saves the [Title|Class|ID] value to the corresponding attribute.
   *
   * @type {converterHandler}
   */
  function converter(event, data, conversionApi) {
    const { item, attributeKey } = data;
    const { consumable, writer } = conversionApi;

    if (!consumable.consume(item, event.name)) {
      return;
    }

    const viewElement = conversionApi.mapper.toViewElement(item);
    const imageInFigure = Array.from(viewElement.getChildren()).find(
      child => child.name === "img"
    );

    writer.setAttribute(
      attributeKey,
      data.attributeNewValue,
      imageInFigure || viewElement
    );
  }

  /**
   * When the callback must be called.
   */
  return dispatcher => {
    dispatcher.on("attribute:title", converter);
    dispatcher.on("attribute:class", converter);
    dispatcher.on("attribute:id", converter);
  };
}
