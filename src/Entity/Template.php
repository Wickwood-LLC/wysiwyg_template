<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\Entity\Template.
 */

namespace Drupal\wysiwyg_template\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\wysiwyg_template\TemplateInterface;

/**
 * Defines the Template entity.
 *
 * @ConfigEntityType(
 *   id = "wysiwyg_template",
 *   label = @Translation("Template"),
 *   handlers = {
 *     "list_builder" = "Drupal\wysiwyg_template\TemplateListBuilder",
 *     "form" = {
 *       "add" = "Drupal\wysiwyg_template\Form\TemplateForm",
 *       "edit" = "Drupal\wysiwyg_template\Form\TemplateForm",
 *       "delete" = "Drupal\wysiwyg_template\Form\TemplateDeleteForm"
 *     }
 *   },
 *   config_prefix = "wysiwyg_template",
 *   admin_permission = "administer wysiwyg templates",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/content/wysiwyg-templates/{wysiwyg_template}",
 *     "edit-form" = "/admin/config/content/wysiwyg-templates/{wysiwyg_template}/edit",
 *     "delete-form" = "/admin/config/content/wysiwyg-templates/{wysiwyg_template}/delete",
 *     "collection" = "/admin/config/content/wysiwyg-templates"
 *   }
 * )
 */
class Template extends ConfigEntityBase implements TemplateInterface {

  /**
   * The unique template ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The template title.
   *
   * @var string
   */
  protected $title;

  /**
   * The template description.
   *
   * @var string
   */
  protected $description;

  /**
   * The template HTML body.
   *
   * @var string
   */
  protected $body;

  /**
   * The template weight.
   *
   * @var integer
   */
  protected $weight;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getBody() {
    if ($body = $this->get('body')) {
      return $body['value'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat() {
    if ($body = $this->get('body')) {
      return $body['format'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }

}
