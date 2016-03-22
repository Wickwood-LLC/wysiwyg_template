/**
 * @file
 * Drupal WYSIWYG template selector
 *
 * @ignore
 */

(function ($, Drupal, CKEDITOR) {

  'use strict';

  CKEDITOR.plugins.add('templateselector', {
    requires: 'templates',
    icons: 'templateselector,templateselector-rtl',
    hidpi: true,

    init: function (editor) {
      // Register the toolbar button.
      if (editor.ui.addButton) {
        editor.ui.addButton('TemplateSelector', {
          label: Drupal.t('Insert template'),
          command: 'templates'
        });
      }

      // Specify path to templates.
      CKEDITOR.config.templates_files = [
        CKEDITOR.getUrl('/wysiwyg-templates/js')
      ];
    }
  });

})(jQuery, Drupal, CKEDITOR);
