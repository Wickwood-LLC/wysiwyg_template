<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\Form\WysiwygTemplateExportForm.
 */

namespace Drupal\wysiwyg_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class WysiwygTemplateExportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_export_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $template = NULL) {
    // @FIXME
// drupal_set_title() has been removed. There are now a few ways to set the title
// dynamically, depending on the situation.
// 
// 
// @see https://www.drupal.org/node/2067859
// drupal_set_title(check_plain($template['title']));

    $code = wysiwyg_template_export_get_code($template);
    $lines = substr_count($code, "\n") + 1;
    $form['export'] = [
      '#title' => t('Export data'),
      '#type' => 'textarea',
      '#value' => $code,
      '#rows' => $lines,
      '#description' => t('Copy the export text and paste it into another site using the import function.'),
    ];

    return $form;
  }

}
