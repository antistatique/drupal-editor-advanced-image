/**
 * @file
 * Drupal Advanced Image plugin.
 *
 * This alters the existing CKEditor image2 widget plugin, which is already
 * altered by the Drupal Image plugin, to:
 * - allow for the title, class & id attributes to be set
 * - mimic the upcasting behavior of the caption_filter filter.
 *
 * @ignore
 */

/* global CKEDITOR */

(function (CKEDITOR) {
  'use strict'

  CKEDITOR.plugins.add('editoradvancedimage', {
    requires: 'drupalimage',

    beforeInit: function (editor) {
      // Retrieve config from Drupal\editor_advanced_image\Plugin\CKEditorPlugin::getConfig.
      var defaultClasses = editor.config.defaultClasses.trim()

      // Override the image2 widget definition to handle the additional
      // title, class and id attributes.
      editor.on('widgetDefinition', function (event) {
        var widgetDefinition = event.data
        if (widgetDefinition.name !== 'image') {
          return
        }

        // Override default features definitions for editoradvancedimage.
        CKEDITOR.tools.extend(widgetDefinition.features, {
          title: {
            requiredContent: 'img[title]'
          },
          class: {
            requiredContent: 'img[class]'
          },
          id: {
            requiredContent: 'img[id]'
          }
        }, true)

        // Extend requiredContent & allowedContent.
        // CKEDITOR.style is an immutable object: we cannot modify its
        // definition to extend requiredContent. Hence we get the definition,
        // modify it, and pass it to a new CKEDITOR.style instance.
        var requiredContent = widgetDefinition.requiredContent.getDefinition()
        requiredContent.attributes['title'] = ''
        requiredContent.attributes['class'] = ''
        requiredContent.attributes['id'] = ''
        widgetDefinition.requiredContent = new CKEDITOR.style(requiredContent)
        widgetDefinition.allowedContent.img.attributes['!title'] = true
        widgetDefinition.allowedContent.img.attributes['!class'] = true
        widgetDefinition.allowedContent.img.attributes['!title'] = true

        // Protected; keys of the widget data to be sent to the Drupal dialog.
        // Append to the values defined by the drupalimage plugin.
        // @see core/modules/ckeditor/js/plugins/drupalimage/plugin.js
        CKEDITOR.tools.extend(widgetDefinition._mapDataToDialog, {
          'title': 'title',
          'class': 'class',
          'id': 'id'
        })

        // Override downcast(): since we only accept <img> in our upcast method,
        // the element is already correct. We only need to update the element's
        // title attribute.
        var originalDowncast = widgetDefinition.downcast
        widgetDefinition.downcast = function (element) {
          var img = findElementByName(element, 'img')
          originalDowncast.call(this, img)

          img.attributes['title'] = this.data['title']
          img.attributes['class'] = this.data['class'] ? this.data['class'].trim() : defaultClasses
          img.attributes['id'] = this.data['id']

          return img
        }

        // We want to upcast <img> elements to a DOM structure required by the
        // image2 widget; we only accept an <img> tag, and that <img> tag MAY
        // have a data-entity-type and a data-entity-uuid attribute.
        var originalUpcast = widgetDefinition.upcast
        widgetDefinition.upcast = function (element, data) {
          if (element.name !== 'img') {
            return
          // Don't initialize on pasted fake objects.
          } else if (element.attributes['data-cke-realelement']) {
            return
          }

          element = originalUpcast.call(this, element, data)

          // Parse the title attribute.
          data['title'] = element.attributes['title']
          // Parse the class attribute & remove default class from it.
          data['class'] = element.attributes['class'] ? element.attributes['class'].trim() : defaultClasses
          // Parse the id attribute.
          data['id'] = element.attributes['id']

          return element
        }

        // Low priority to ensure drupalimage's event handler runs first.
      }, null, null, 20)
    }
  })

  /**
   * Finds an element by its name.
   *
   * Function will check first the passed element itself and then all its
   * children in DFS order.
   *
   * @param {CKEDITOR.htmlParser.element} element
   *   The element to search.
   * @param {string} name
   *   The element name to search for.
   *
   * @return {?CKEDITOR.htmlParser.element}
   *   The found element, or null.
   */
  function findElementByName (element, name) {
    if (element.name === name) {
      return element
    }

    var found = null
    element.forEach(function (el) {
      if (el.name === name) {
        found = el
        // Stop here.
        return false
      }
    }, CKEDITOR.NODE_ELEMENT)
    return found
  }
})(CKEDITOR)
