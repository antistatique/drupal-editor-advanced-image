/**
 * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImage
 */

import { Plugin } from "ckeditor5/src/core";
import EditorAdvancedImageEditing from "./EditorAdvancedImageEditing";
import EditorAdvancedImageUi from "./EditorAdvancedImageUI";
import EditorAdvancedImageCommand from "./EditorAdvancedImageCommand";

/**
 * The Editor Advanced Image plugin.
 *
 * This has been implemented based on the CKEditor 5 built in image alternative
 * text plugin. This plugin enhances the original upstream form with a toggle
 * button that allows users to explicitly change image attributes, which is
 * downcast to `title`, `id`, `class` attribute.
 *
 * @see module:image/imagetextalternative~ImageTextAlternative
 *
 * @extends module:core/plugin~Plugin
 */
class EditorAdvancedImage extends Plugin {
  /**
   * @inheritdoc
   */
  static get requires() {
    return [
      EditorAdvancedImageEditing,
      EditorAdvancedImageUi,
      EditorAdvancedImageCommand
    ];
  }

  /**
   * @inheritdoc
   */
  static get pluginName() {
    return "EditorAdvancedImage";
  }
}

export default {
  EditorAdvancedImage
};
