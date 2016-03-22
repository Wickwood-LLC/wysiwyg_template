<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\Form\WysiwygTemplateImportForm.
 */

namespace Drupal\wysiwyg_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class WysiwygTemplateImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_import_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['import'] = [
      '#title' => t('Import data'),
      '#type' => 'textarea',
      '#rows' => 20,
      '#description' => t('Paste the code from template export function.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];
    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if ($form_state->getUserInput()) {
      wysiwyg_template_import_from_code($form_state->getUserInput());
      $form_state->set(['redirect'], 'admin/config/content/wysiwyg-templates');
    }
    else {
      $form_state->setErrorByName('import', '$template');
    }
  }

}
