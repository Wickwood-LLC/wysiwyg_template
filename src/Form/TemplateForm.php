<?php

namespace Drupal\wysiwyg_template\Form;

use Drupal;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Class TemplateForm.
 *
 * @package Drupal\wysiwyg_template\Form
 */
class TemplateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\wysiwyg_template_core\TemplateInterface $wysiwyg_template */
    $wysiwyg_template = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#default_value' => $wysiwyg_template->label(),
      '#description' => $this->t('Select a name for this template.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $wysiwyg_template->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\wysiwyg_template\Entity\Template::load',
      ),
      '#disabled' => !$wysiwyg_template->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textfield',
      '#default_value' => $wysiwyg_template->getDescription(),
      '#title' => $this->t('Description'),
      '#description' => $this->t('A description to be shown with the template.'),
    ];

    $form['body'] = [
      '#type' => 'text_format',
      '#format' => $wysiwyg_template->getFormat(),
      '#default_value' => $wysiwyg_template->getBody(),
      '#title' => $this->t('HTML template'),
      '#rows' => 10,
      '#required' => TRUE,
    ];

    $form['entity_types'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Available for:'),
      '#description' => $this->t('If you select no type in a section, this template will be available for all types of that section.'),
      '#tree' => TRUE,
    ];
    foreach (Drupal::service('wysiwyg_template.services')->getEntityTypesAndBundles() as $id => $defintion) {
      if (!empty($defintion['bundles'])) {
        $types = [];
        foreach ($defintion['bundles'] as $bundleId => $bundeDef) {
          $types[$bundleId] = $bundeDef['label'];
        }
        $form['entity_types'][$id] = [
          '#type' => 'checkboxes',
          '#default_value' => $wysiwyg_template->getBundles($id),
          '#title' => $defintion['label'],
          '#options' => $types,
        ];
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\wysiwyg_template_core\TemplateInterface $wysiwyg_template */
    $wysiwyg_template = $this->entity;
    $status = $wysiwyg_template->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label template.', [
          '%label' => $wysiwyg_template->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label template.', [
          '%label' => $wysiwyg_template->label(),
        ]));
    }
    $form_state->setRedirectUrl($wysiwyg_template->toUrl('collection'));
  }

}
