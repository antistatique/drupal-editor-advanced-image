!function(e,n){"object"==typeof exports&&"object"==typeof module?module.exports=n():"function"==typeof define&&define.amd?define([],n):"object"==typeof exports?exports.CKEditor5=n():(e.CKEditor5=e.CKEditor5||{},e.CKEditor5.editorAdvancedImage=n())}(self,(()=>(()=>{var __webpack_modules__={"./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageCommand.js":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (/* binding */ EditorAdvancedImageCommand)\n/* harmony export */ });\n/* harmony import */ var ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor5/src/core */ "ckeditor5/src/core.js");\n/**\n * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImageCommand\n */\n\n\n\n/**\n * The Editor Advanced Image Command plugin\n *\n * @extends module:core/command~Command\n *\n * @private\n */\nclass EditorAdvancedImageCommand extends ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__.Command {\n  /**\n   * Constructs a new object.\n   *\n   * @param {module:core/editor/editor~Editor} editor\n   *   The editor instance.\n   * @param {Object<string>} options\n   *   All available Drupal Editor Advanced Image options.\n   */\n  constructor(editor, options) {\n    super(editor);\n    this.options = options;\n  }\n\n  /**\n   * @inheritdoc\n   *\n   * Will be used on every element to store current attributes values.\n   */\n  refresh() {\n    const editor = this.editor;\n    const imageUtils = editor.plugins.get("ImageUtils");\n    const element = imageUtils.getClosestSelectedImageElement(\n      this.editor.model.document.selection\n    );\n\n    this.isEnabled = !!element;\n\n    this.attributes = {\n      title: false,\n      id: false,\n      class: false\n    };\n\n    // Don\'t store any values\n    if (!this.isEnabled) {\n      return;\n    }\n\n    // Set the default class configured on the Drupal Editor Plugin.\n    if (\n      this.options !== undefined &&\n      this.options.defaults.class !== undefined\n    ) {\n      this.attributes.class = this.options.defaults.class;\n    }\n\n    // Store the title attribute value when on an image element.\n    if (element.hasAttribute("title")) {\n      this.attributes.title = element.getAttribute("title");\n    }\n\n    // Store the class attribute value when on an image element.\n    if (element.hasAttribute("class")) {\n      this.attributes.class = element.getAttribute("class");\n    }\n\n    // Store the id attribute value when on an image element.\n    if (element.hasAttribute("id")) {\n      this.attributes.id = element.getAttribute("id");\n    }\n\n    // Force an execution at refresh time in order to set attributes even when the Balloon form has still not been used.\n    this.execute(this.attributes);\n  }\n\n  /**\n   * Set attributes to the element.\n   */\n  execute(attributes) {\n    const editor = this.editor;\n    const imageUtils = editor.plugins.get("ImageUtils");\n    const model = editor.model;\n    const imageElement = imageUtils.getClosestSelectedImageElement(\n      model.document.selection\n    );\n\n    if (attributes.title) {\n      model.change(writer =>\n        writer.setAttribute("title", attributes.title, imageElement)\n      );\n    }\n\n    if (attributes.id) {\n      model.change(writer =>\n        writer.setAttribute("id", attributes.id, imageElement)\n      );\n    }\n\n    if (attributes.class) {\n      model.change(writer =>\n        writer.setAttribute("class", attributes.class, imageElement)\n      );\n    }\n  }\n}\n\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageCommand.js?')},"./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageEditing.js":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (/* binding */ EditorAdvancedImageEditing)\n/* harmony export */ });\n/* harmony import */ var ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor5/src/core */ "ckeditor5/src/core.js");\n/* harmony import */ var _EditorAdvancedImageCommand__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EditorAdvancedImageCommand */ "./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageCommand.js");\n/**\n * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImageCommand\n */\n\n\n\n\n/**\n * The Editor Advanced Image Editing plugin.\n *\n * Registers the `EditorAdvancedImage` command.\n *\n * @extends module:core/plugin~Plugin\n *\n * @internal\n */\nclass EditorAdvancedImageEditing extends ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__.Plugin {\n  /**\n   * @inheritdoc\n   */\n  static get requires() {\n    return ["ImageUtils"];\n  }\n\n  /**\n   * @inheritdoc\n   */\n  static get pluginName() {\n    return "EditorAdvancedImageEditing";\n  }\n\n  init() {\n    const editor = this.editor;\n\n    // Get the Drupal Configurations.\n    // @see \\Drupal\\editor_advanced_image\\Plugin\\CKEditor5Plugin\\EditorAdvancedImage::getDynamicPluginConfig\n    const options = editor.config.get(`editorAdvancedImageOptions`);\n\n    this._defineSchema();\n    this._defineConverters();\n\n    // Declare our command.\n    editor.commands.add(\n      "EditorAdvancedImageCmd",\n      new _EditorAdvancedImageCommand__WEBPACK_IMPORTED_MODULE_1__["default"](editor, options)\n    );\n  }\n\n  /**\n   * Define Schema with allowed attributes that will automatically be used by\n   * Drupal "\tLimit allowed HTML tags and correct faulty HTML".\n   */\n  _defineSchema() {\n    const { editor } = this;\n    const { schema } = editor.model;\n\n    if (schema.isRegistered("imageInline")) {\n      schema.extend("imageInline", {\n        allowAttributes: ["title", "class", "id"]\n      });\n    }\n\n    if (schema.isRegistered("imageBlock")) {\n      schema.extend("imageBlock", {\n        allowAttributes: ["title", "class", "id"]\n      });\n    }\n  }\n\n  _defineConverters() {\n    const { editor } = this;\n    const { conversion } = this.editor;\n    const { schema } = editor.model;\n\n    // Upcast Converters: determine how existing HTML is interpreted by the\n    // editor. These trigger when an editor instance loads.\n    conversion.for("upcast").attributeToAttribute({\n      model: "title",\n      view: "title"\n    });\n    conversion.for("upcast").attributeToAttribute({\n      model: "id",\n      view: "id"\n    });\n    conversion.for("upcast").attributeToAttribute({\n      model: "class",\n      view: "class"\n    });\n\n    // Data Downcast Converters: converts stored model data into HTML.\n    // These trigger when content is saved.\n    conversion\n      .for("downcast")\n      // We can\'t use \'dataDowncast\'.attributeToAttribute as we want to apply attributes on the <img> and\n      // not the <figure> element.\n      .add(modelAttributeToDataAttribute());\n  }\n}\n\n/**\n * Generates a callback that saves the attributes value to an attribute on\n * data downcast.\n *\n * @return {function}\n *  Callback that binds an event to it\'s parameter.\n *\n * @private\n */\nfunction modelAttributeToDataAttribute() {\n  /**\n   * Callback for the attribute:[title|class|id] event.\n   *\n   * Saves the [Title|Class|ID] value to the corresponding attribute.\n   *\n   * @type {converterHandler}\n   */\n  function converter(event, data, conversionApi) {\n    const { item, attributeKey } = data;\n    const { consumable, writer } = conversionApi;\n\n    if (!consumable.consume(item, event.name)) {\n      return;\n    }\n\n    const viewElement = conversionApi.mapper.toViewElement(item);\n    const imageInFigure = Array.from(viewElement.getChildren()).find(\n      child => child.name === "img"\n    );\n\n    writer.setAttribute(\n      attributeKey,\n      data.attributeNewValue,\n      imageInFigure || viewElement\n    );\n  }\n\n  /**\n   * When the callback must be called.\n   */\n  return dispatcher => {\n    dispatcher.on("attribute:title", converter);\n    dispatcher.on("attribute:class", converter);\n    dispatcher.on("attribute:id", converter);\n  };\n}\n\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageEditing.js?')},"./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageUI.js":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (/* binding */ EditorAdvancedImageUI)\n/* harmony export */ });\n/* harmony import */ var ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor5/src/core */ "ckeditor5/src/core.js");\n/* harmony import */ var ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ckeditor5/src/ui */ "ckeditor5/src/ui.js");\n/* harmony import */ var _ckeditor_ckeditor5_image_src_image_ui_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @ckeditor/ckeditor5-image/src/image/ui/utils */ "./node_modules/@ckeditor/ckeditor5-image/src/image/ui/utils.js");\n/* harmony import */ var _ui_EditorAdvancedImageFormView__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ui/EditorAdvancedImageFormView */ "./js/ckeditor5_plugins/editorAdvancedImage/src/ui/EditorAdvancedImageFormView.js");\n/**\n * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImageUI\n */\n\n\n\n\n\n\n\n\n/**\n * The Editor Advanced Image UI plugin.\n *\n * The plugin uses the contextual balloon.\n *\n * @see module:ui/panel/balloon/contextualballoon~ContextualBalloon\n *\n * @extends module:core/plugin~Plugin\n *\n * @internal\n */\nclass EditorAdvancedImageUI extends ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__.Plugin {\n  /**\n   * @inheritdoc\n   */\n  static get requires() {\n    return [ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_1__.ContextualBalloon];\n  }\n\n  /**\n   * @inheritdoc\n   */\n  init() {\n    const options = this.editor.config.get("editorAdvancedImageOptions");\n\n    // Prevent creation of Editor Advanced Button & Form when the option "Disable Balloon" is enabled.\n    if (\n      options !== undefined &&\n      options.disable_balloon !== undefined &&\n      options.disable_balloon\n    ) {\n      return;\n    }\n\n    this._createButton();\n    this._createForm();\n  }\n\n  /**\n   * @inheritdoc\n   */\n  destroy() {\n    super.destroy();\n\n    // Destroy created UI components as they are not automatically destroyed\n    // @see https://github.com/ckeditor/ckeditor5/issues/1341\n    this._form.destroy();\n  }\n\n  /**\n   * Creates a button showing the balloon panel for changing the image advanced\n   * attributes alternative and registers it in the editor component factory.\n   *\n   * @see module:ui/componentfactory~ComponentFactory\n   *\n   * @private\n   */\n  _createButton() {\n    const editor = this.editor;\n\n    // The name on the component factory must be the same as in editor_advanced_image.ckeditor5.yml.\n    editor.ui.componentFactory.add("editorAdvancedImageButton", locale => {\n      const command = editor.commands.get("EditorAdvancedImageCmd");\n      const view = new ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_1__.ButtonView(locale);\n\n      view.set({\n        label: Drupal.t("Editor Advanced Image"),\n        icon: ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__.icons.threeVerticalDots,\n        tooltip: true\n      });\n\n      // Enable the UI if and only if the command is enabled.\n      view.bind("isVisible").to(command, "isEnabled");\n\n      this.listenTo(view, "execute", () => {\n        this._showForm();\n      });\n\n      return view;\n    });\n  }\n\n  /**\n   * Creates the {@link module:editor_advanced_image/editorAdvancedImage/ui/EditorAdvancedImageFormView~EditorAdvancedImageFormView}\n   * form.\n   *\n   * @private\n   */\n  _createForm() {\n    const editor = this.editor;\n\n    // Get the Drupal Configurations.\n    // @see \\Drupal\\editor_advanced_image\\Plugin\\CKEditor5Plugin\\EditorAdvancedImage::getDynamicPluginConfig\n    const options = editor.config.get(`editorAdvancedImageOptions`);\n\n    /**\n     * The contextual balloon plugin instance.\n     */\n    this._balloon = this.editor.plugins.get("ContextualBalloon");\n\n    /**\n     * A form containing class, title & id inputs, used to change data-attributes values.\n     */\n    this._form = new _ui_EditorAdvancedImageFormView__WEBPACK_IMPORTED_MODULE_3__["default"](editor.locale, options);\n\n    // Render the form so its #element is available for clickOutsideHandler.\n    this._form.render();\n\n    // When the form is submitted, close the form instead of POST the whole page.\n    this.listenTo(this._form, "save", () => {\n      // Execute the Command.\n      editor.execute("EditorAdvancedImageCmd", {\n        title: options.allowedAttributes.includes("title")\n          ? this._form.titleAttrInput.fieldView.element.value\n          : false,\n        class: options.allowedAttributes.includes("class")\n          ? this._form.classAttrInput.fieldView.element.value\n          : false,\n        id: options.allowedAttributes.includes("id")\n          ? this._form.idAttrInput.fieldView.element.value\n          : false\n      });\n\n      this._hideForm(true);\n    });\n\n    // Close the form when clicking on the cancel button.\n    this.listenTo(this._form, "cancel", () => {\n      this._hideForm(true);\n    });\n\n    // Close the form on Esc key press.\n    this._form.keystrokes.set("Esc", (data, cancel) => {\n      this._hideForm(true);\n      cancel();\n    });\n\n    // Close on click outside of balloon panel element.\n    (0,ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_1__.clickOutsideHandler)({\n      emitter: this._form,\n      activator: () => this._isVisible,\n      contextElements: [this._balloon.view.element],\n      callback: () => this._hideForm()\n    });\n  }\n\n  /**\n   * Shows the form in a balloon.\n   */\n  _showForm() {\n    if (this._isVisible) {\n      return;\n    }\n\n    const editor = this.editor;\n    const command = editor.commands.get("EditorAdvancedImageCmd");\n\n    // Get the Drupal Configurations.\n    // @see \\Drupal\\editor_advanced_image\\Plugin\\CKEditor5Plugin\\EditorAdvancedImage::getDynamicPluginConfig\n    const options = editor.config.get(`editorAdvancedImageOptions`);\n\n    // this._form.disableCssTransitions();\n\n    if (!this._isInBalloon) {\n      // Place the form into the Balloon.\n      this._balloon.add({\n        view: this._form,\n        // Be sure to keep the Balloon at the same centered position.\n        position: (0,_ckeditor_ckeditor5_image_src_image_ui_utils__WEBPACK_IMPORTED_MODULE_2__.getBalloonPositionData)(editor)\n      });\n    }\n\n    // Make sure that each time the panel shows up, the field remains in sync with the value of\n    // the command. If the user typed in the input, then canceled the balloon (`labeledInput#value`\n    // stays unaltered) and re-opened it without changing the value of the command, they would see the\n    // old value instead of the actual value of the command.\n    // https://github.com/ckeditor/ckeditor5-image/issues/114\n    if (options.allowedAttributes.includes("title")) {\n      this._form.titleAttrInput.fieldView.element.value =\n        command.attributes.title || "";\n      this._form.titleAttrInput.fieldView.value = this._form.titleAttrInput.fieldView.element.value;\n    }\n\n    if (options.allowedAttributes.includes("class")) {\n      this._form.classAttrInput.fieldView.element.value =\n        command.attributes.class || "";\n      this._form.classAttrInput.fieldView.value = this._form.classAttrInput.fieldView.element.value;\n    }\n\n    if (options.allowedAttributes.includes("id")) {\n      this._form.idAttrInput.fieldView.element.value =\n        command.attributes.id || "";\n      this._form.idAttrInput.fieldView.value = this._form.idAttrInput.fieldView.element.value;\n    }\n    // this._form.enableCssTransitions();\n  }\n\n  /**\n   * Removes the {@link #_form} from the {@link #_balloon}.\n   *\n   * @param {Boolean} [focusEditable=false] Controls whether the editing view is focused afterwards.\n   * @private\n   */\n  _hideForm(focusEditable) {\n    if (!this._isInBalloon) {\n      return;\n    }\n\n    this._balloon.remove(this._form);\n\n    if (focusEditable) {\n      this.editor.editing.view.focus();\n    }\n  }\n\n  /**\n   * Returns `true` when the form is the visible view in the balloon.\n   *\n   * @type {Boolean}\n   */\n  get _isVisible() {\n    return this._balloon.visibleView === this._form;\n  }\n\n  /**\n   * Returns `true` when the form is in the balloon.\n   *\n   * @type {Boolean}\n   */\n  get _isInBalloon() {\n    return this._balloon.hasView(this._form);\n  }\n}\n\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageUI.js?')},"./js/ckeditor5_plugins/editorAdvancedImage/src/index.js":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor5/src/core */ "ckeditor5/src/core.js");\n/* harmony import */ var _EditorAdvancedImageEditing__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EditorAdvancedImageEditing */ "./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageEditing.js");\n/* harmony import */ var _EditorAdvancedImageUI__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./EditorAdvancedImageUI */ "./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageUI.js");\n/* harmony import */ var _EditorAdvancedImageCommand__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./EditorAdvancedImageCommand */ "./js/ckeditor5_plugins/editorAdvancedImage/src/EditorAdvancedImageCommand.js");\n/**\n * @module editor_advanced_image/editorAdvancedImage/EditorAdvancedImage\n */\n\n\n\n\n\n\n/**\n * The Editor Advanced Image plugin.\n *\n * This has been implemented based on the CKEditor 5 built in image alternative\n * text plugin. This plugin enhances the original upstream form with a toggle\n * button that allows users to explicitly change image attributes, which is\n * downcast to `title`, `id`, `class` attribute.\n *\n * @see module:image/imagetextalternative~ImageTextAlternative\n *\n * @extends module:core/plugin~Plugin\n */\nclass EditorAdvancedImage extends ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_0__.Plugin {\n  /**\n   * @inheritdoc\n   */\n  static get requires() {\n    return [\n      _EditorAdvancedImageEditing__WEBPACK_IMPORTED_MODULE_1__["default"],\n      _EditorAdvancedImageUI__WEBPACK_IMPORTED_MODULE_2__["default"],\n      _EditorAdvancedImageCommand__WEBPACK_IMPORTED_MODULE_3__["default"]\n    ];\n  }\n\n  /**\n   * @inheritdoc\n   */\n  static get pluginName() {\n    return "EditorAdvancedImage";\n  }\n}\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({\n  EditorAdvancedImage\n});\n\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/./js/ckeditor5_plugins/editorAdvancedImage/src/index.js?')},"./js/ckeditor5_plugins/editorAdvancedImage/src/ui/EditorAdvancedImageFormView.js":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{"use strict";eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (/* binding */ EditorAdvancedImageFormView)\n/* harmony export */ });\n/* harmony import */ var ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor5/src/ui */ "ckeditor5/src/ui.js");\n/* harmony import */ var ckeditor5_src_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ckeditor5/src/utils */ "ckeditor5/src/utils.js");\n/* harmony import */ var ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ckeditor5/src/core */ "ckeditor5/src/core.js");\n/**\n * @module editor_advanced_image/editorAdvancedImage/ui/EditorAdvancedImageFormView\n */\n\n\n\n\n\n/**\n * A class rendering Editor Advanced Image form view.\n *\n * @extends module:ui/view~View\n *\n * @internal\n */\nclass EditorAdvancedImageFormView extends ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__.View {\n  /**\n   * @inheritdoc\n   */\n  constructor(locale, options) {\n    super(locale);\n\n    this.options = options;\n\n    /**\n     * An instance of the {@link module:utils/keystrokehandler~KeystrokeHandler}.\n     *\n     * @readonly\n     * @member {module:utils/keystrokehandler~KeystrokeHandler}\n     */\n    this.keystrokes = new ckeditor5_src_utils__WEBPACK_IMPORTED_MODULE_1__.KeystrokeHandler();\n\n    /**\n     * The form title.\n     *\n     * @member {module:ui/label/labelview~LabelView} #classAttrInput\n     */\n    this.formTitle = this._createLabelView("Editor Advanced Image");\n\n    /**\n     * The Class attribute input.\n     *\n     * @member {module:ui/labeledfield/labeledfieldview~LabeledFieldView} #classAttrInput\n     */\n    this.classAttrInput = this._createLabeledInputView(\n      "CSS classes",\n      "List of CSS classes to be added to the image, separated by spaces."\n    );\n\n    /**\n     * The Title attribute input .\n     *\n     * @member {module:ui/labeledfield/labeledfieldview~LabeledFieldView} #titleAttrInput\n     */\n    this.titleAttrInput = this._createLabeledInputView(\n      "Title",\n      "Populates the title attribute of the image, usually shown as a small tooltip on hover."\n    );\n\n    /**\n     * The ID attribute input .\n     *\n     * @member {module:ui/labeledfield/labeledfieldview~LabeledFieldView} #idAttrInput\n     */\n    this.idAttrInput = this._createLabeledInputView(\n      "ID",\n      "Usually used to linking to this content using a https://en.wikipedia.org/wiki/Fragment_identifier. Must be unique on the page."\n    );\n\n    /**\n     * A button used to submit the form.\n     *\n     * @member {module:ui/button/buttonview~ButtonView} #saveButtonView\n     */\n    this.saveButtonView = this._createButton(\n      Drupal.t("Save"),\n      ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_2__.icons.check,\n      "ck-button-save",\n      "save"\n    );\n\n    /**\n     * A button used to cancel the form.\n     *\n     * @member {module:ui/button/buttonview~ButtonView} #cancelButtonView\n     */\n    this.cancelButtonView = this._createButton(\n      Drupal.t("Cancel"),\n      ckeditor5_src_core__WEBPACK_IMPORTED_MODULE_2__.icons.cancel,\n      "ck-button-cancel",\n      "cancel"\n    );\n\n    // Build the children form items.\n    const children = [\n      {\n        tag: "div",\n        attributes: {\n          class: ["ck"]\n        },\n        children: [this.formTitle]\n      }\n    ];\n\n    // Add each inputs only if the attribute is allowed.\n    if (this.options.allowedAttributes.includes("class")) {\n      children.push(this.classAttrInput);\n    }\n\n    // Add each inputs only if the attribute is allowed.\n    if (this.options.allowedAttributes.includes("title")) {\n      children.push(this.titleAttrInput);\n    }\n\n    // Add each inputs only if the attribute is allowed.\n    if (this.options.allowedAttributes.includes("id")) {\n      children.push(this.idAttrInput);\n    }\n\n    children.push(this.saveButtonView);\n    children.push(this.cancelButtonView);\n\n    this.setTemplate({\n      tag: "form",\n\n      attributes: {\n        class: [\n          "ck",\n          "ck-vertical-form",\n          "ck-editor-advanced-image",\n          "ck-responsive-form"\n        ],\n\n        // https://github.com/ckeditor/ckeditor5-image/issues/40\n        tabindex: "-1"\n      },\n\n      children\n    });\n\n    // injectCssTransitionDisabler(this);\n  }\n\n  /**\n   * @inheritdoc\n   */\n  destroy() {\n    super.destroy();\n\n    this.keystrokes.destroy();\n  }\n\n  /**\n   * Creates a label.\n   *\n   * @returns {module:ui/label/labelview~LabelView}\n   *   Labeled view instance.\n   *\n   * @private\n   */\n  _createLabelView(text) {\n    const label = new ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__.LabelView(this.locale);\n    label.text = Drupal.t(text);\n    return label;\n  }\n\n  /**\n   * Creates the button view.\n   *\n   * @param {String} label\n   *   The button label\n   * @param {String} icon\n   *   The button\'s icon.\n   * @param {String} className\n   *   The additional button CSS class name.\n   * @param {String} [eventName]\n   *   The event name that the ButtonView#execute event will be delegated to.\n   * @returns {module:ui/button/buttonview~ButtonView}\n   *   The button view instance.\n   *\n   * @private\n   */\n  _createButton(label, icon, className, eventName) {\n    const button = new ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__.ButtonView(this.locale);\n\n    button.set({\n      label,\n      icon,\n      tooltip: true\n    });\n\n    button.extendTemplate({\n      attributes: {\n        class: className\n      }\n    });\n\n    if (eventName) {\n      button.delegate("execute").to(this, eventName);\n    }\n\n    return button;\n  }\n\n  /**\n   * Creates an input with a label.\n   *\n   * @returns {module:ui/labeledfield/labeledfieldview~LabeledFieldView}\n   *   Labeled field view instance.\n   *\n   * @private\n   */\n  _createLabeledInputView(label, infoText) {\n    const labeledInput = new ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__.LabeledFieldView(\n      this.locale,\n      ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__.createLabeledInputText\n    );\n\n    labeledInput.label = Drupal.t(label);\n    labeledInput.infoText = Drupal.t(infoText);\n\n    return labeledInput;\n  }\n}\n\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/./js/ckeditor5_plugins/editorAdvancedImage/src/ui/EditorAdvancedImageFormView.js?')},"./node_modules/@ckeditor/ckeditor5-image/src/image/ui/utils.js":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{"use strict";eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"getBalloonPositionData\": () => (/* binding */ getBalloonPositionData),\n/* harmony export */   \"repositionContextualBalloon\": () => (/* binding */ repositionContextualBalloon)\n/* harmony export */ });\n/* harmony import */ var ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ckeditor5/src/ui */ \"ckeditor5/src/ui.js\");\n/**\n * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.\n * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license\n */\n\n/**\n * @module image/image/ui/utils\n */\n\n\n\n/**\n * A helper utility that positions the\n * {@link module:ui/panel/balloon/contextualballoon~ContextualBalloon contextual balloon} instance\n * with respect to the image in the editor content, if one is selected.\n *\n * @param {module:core/editor/editor~Editor} editor The editor instance.\n */\nfunction repositionContextualBalloon( editor ) {\n\tconst balloon = editor.plugins.get( 'ContextualBalloon' );\n\n\tif ( editor.plugins.get( 'ImageUtils' ).getClosestSelectedImageWidget( editor.editing.view.document.selection ) ) {\n\t\tconst position = getBalloonPositionData( editor );\n\n\t\tballoon.updatePosition( position );\n\t}\n}\n\n/**\n * Returns the positioning options that control the geometry of the\n * {@link module:ui/panel/balloon/contextualballoon~ContextualBalloon contextual balloon} with respect\n * to the selected element in the editor content.\n *\n * @param {module:core/editor/editor~Editor} editor The editor instance.\n * @returns {module:utils/dom/position~Options}\n */\nfunction getBalloonPositionData( editor ) {\n\tconst editingView = editor.editing.view;\n\tconst defaultPositions = ckeditor5_src_ui__WEBPACK_IMPORTED_MODULE_0__.BalloonPanelView.defaultPositions;\n\tconst imageUtils = editor.plugins.get( 'ImageUtils' );\n\n\treturn {\n\t\ttarget: editingView.domConverter.viewToDom( imageUtils.getClosestSelectedImageWidget( editingView.document.selection ) ),\n\t\tpositions: [\n\t\t\tdefaultPositions.northArrowSouth,\n\t\t\tdefaultPositions.northArrowSouthWest,\n\t\t\tdefaultPositions.northArrowSouthEast,\n\t\t\tdefaultPositions.southArrowNorth,\n\t\t\tdefaultPositions.southArrowNorthWest,\n\t\t\tdefaultPositions.southArrowNorthEast,\n\t\t\tdefaultPositions.viewportStickyNorth\n\t\t]\n\t};\n}\n\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/./node_modules/@ckeditor/ckeditor5-image/src/image/ui/utils.js?")},"ckeditor5/src/core.js":(module,__unused_webpack_exports,__webpack_require__)=>{eval('module.exports = (__webpack_require__(/*! dll-reference CKEditor5.dll */ "dll-reference CKEditor5.dll"))("./src/core.js");\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/delegated_./core.js_from_dll-reference_CKEditor5.dll?')},"ckeditor5/src/ui.js":(module,__unused_webpack_exports,__webpack_require__)=>{eval('module.exports = (__webpack_require__(/*! dll-reference CKEditor5.dll */ "dll-reference CKEditor5.dll"))("./src/ui.js");\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/delegated_./ui.js_from_dll-reference_CKEditor5.dll?')},"ckeditor5/src/utils.js":(module,__unused_webpack_exports,__webpack_require__)=>{eval('module.exports = (__webpack_require__(/*! dll-reference CKEditor5.dll */ "dll-reference CKEditor5.dll"))("./src/utils.js");\n\n//# sourceURL=webpack://CKEditor5.editorAdvancedImage/delegated_./utils.js_from_dll-reference_CKEditor5.dll?')},"dll-reference CKEditor5.dll":e=>{"use strict";e.exports=CKEditor5.dll}},__webpack_module_cache__={};function __webpack_require__(e){var n=__webpack_module_cache__[e];if(void 0!==n)return n.exports;var t=__webpack_module_cache__[e]={exports:{}};return __webpack_modules__[e](t,t.exports,__webpack_require__),t.exports}__webpack_require__.d=(e,n)=>{for(var t in n)__webpack_require__.o(n,t)&&!__webpack_require__.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:n[t]})},__webpack_require__.o=(e,n)=>Object.prototype.hasOwnProperty.call(e,n),__webpack_require__.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var __webpack_exports__=__webpack_require__("./js/ckeditor5_plugins/editorAdvancedImage/src/index.js");return __webpack_exports__=__webpack_exports__.default,__webpack_exports__})()));