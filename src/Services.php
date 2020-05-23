<?php


namespace Drupal\wysiwyg_template;


use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class Services {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $bundleInfo;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $bundle_info, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->bundleInfo = $bundle_info;
    $this->entityFieldManager = $entity_field_manager;
  }

  public function getEntityTypesAndBundles() {
    $entity_types = [];
    foreach ($this->entityTypeManager->getDefinitions() as $definition) {
      if ($definition instanceof ContentEntityTypeInterface) {
        $bundles = array_keys($this->bundleInfo->getBundleInfo($definition->id()));
        if (empty($bundles)) {
          $bundles = [$definition->id()];
        }
        $entity_types[$definition->id()] = [
          'label' => $definition->getLabel(),
          'bundles' => [],
        ];
        foreach ($bundles as $bundle) {
          $text_fields = [];
          foreach ($this->entityFieldManager->getFieldDefinitions($definition->id(), $bundle) as $fieldDefinition) {
            if (in_array($fieldDefinition->getType(), ['text', 'text_long', 'text_with_summary'])) {
              $text_fields[] = $fieldDefinition;
            }
          }
          if (!empty($text_fields)) {
            $entity_types[$definition->id()]['bundles'][$bundle] = [
              'label' => $bundle,
              'fields' => $text_fields,
            ];
          }
        }
      }
    }
    return $entity_types;
  }

}
