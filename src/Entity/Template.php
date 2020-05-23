<?php

namespace Drupal\wysiwyg_template\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\wysiwyg_template_core\TemplateInterface;

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
 *     "uuid" = "uuid",
 *     "weight" = "weight"
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
   * The node types this template is available for.
   *
   * @var string[]
   */
  protected $node_types;

  /**
   * The entity types this template is available for.
   *
   * @var string[][]
   */
  protected $entity_types;

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->description ?: '';
  }

  /**
   * {@inheritdoc}
   */
  public function getBody(): string {
    if ($body = $this->get('body')) {
      return $body['value'];
    }
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormat(): string {
    if ($body = $this->get('body')) {
      return $body['format'];
    }
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeTypes(): array {
    return $this->node_types ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getBundles($entity_type = NULL): array {
    if ($entity_type === NULL) {
      return empty($this->entity_types) ? [] : array_keys($this->entity_types);
    }
    return $this->entity_types[$entity_type] ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function setBundles($entity_type, array $bundles): TemplateInterface {
    $this->entity_types[$entity_type] = $bundles;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    $this->node_types = array_values(array_filter($this->getNodeTypes()));
    foreach ($this->getBundles() as $type) {
      $this->entity_types[$type] = array_values(array_filter($this->getBundles($type)));
    }
    parent::save();
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);
    // Sort the queried roles by their weight.
    // See \Drupal\Core\Config\Entity\ConfigEntityBase::sort().
    uasort($entities, 'static::sort');
  }

  /**
   * {@inheritdoc}
   */
  public static function loadByNodeType(NodeTypeInterface $node_type = NULL): array {
    /** @var \Drupal\wysiwyg_template_core\TemplateInterface[] $templates */
    $templates = static::loadMultiple();
    foreach ($templates as $id => $template) {
      if (!$node_type) {
        // If no node type is passed than all templates that *don't specify any*
        // types are included, but those specifying a type are not.
        if (!empty($template->getNodeTypes())) {
          unset($templates[$id]);
        }
      }
      else {
        // Any templates without types, plus the templates that specify this type.
        if (empty($template->getNodeTypes()) || in_array($node_type->id(), $template->getNodeTypes(), TRUE)) {
          continue;
        }
        unset($templates[$id]);
      }
    }

    return $templates;
  }

  /**
   * {@inheritdoc}
   */
  public static function loadByTypeAndBundle($entity_type, $bundle): array {
    /** @var \Drupal\wysiwyg_template_core\TemplateInterface[] $templates */
    $templates = static::loadMultiple();
    foreach ($templates as $id => $template) {
      $bundles = $template->getBundles($entity_type);
      if (!empty($bundles)) {
        if (!in_array($bundle, $bundles)) {
          // At least one bundle of the entity type is selected but not the given
          // one, so this template is not for us.
          unset($templates[$id]);
        }
        // Otherwise, we are relevant for the given bundle.
      }
      else {
        foreach ($template->getBundles() as $type) {
          if ($type === $entity_type) {
            // Do not test the current type again.
            continue;
          }
          if (!empty($template->getBundles($entity_type))) {
            // The template is selected for another entity type, so it's not for us.
            unset($templates[$id]);
            continue 2;
          }
        }
      }
    }
    return $templates;
  }

}
