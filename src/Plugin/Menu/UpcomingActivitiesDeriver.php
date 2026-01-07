<?php

namespace Drupal\acdweb_tools\Plugin\Menu;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Derives menu links for upcoming 'activiteit' nodes.
 */
class UpcomingActivitiesDeriver extends DeriverBase implements ContainerDeriverInterface {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function getDerivativeDefinitions($base_plugin_definition) {
    // 1. SETUP PARENT & MENU
    $parentUuid = '51ed2590-3345-4271-935c-0d0f409d6325';
    $parentId = 'menu_link_content:' . $parentUuid;
    
    // Attempt to load parent to ensure dynamic menu name correctness
    $menuName = 'main'; // Default fallback
    $parent_entities = $this->entityTypeManager->getStorage('menu_link_content')->loadByProperties(['uuid' => $parentUuid]);
    if ($parent_entity = reset($parent_entities)) {
      $menuName = $parent_entity->getMenuName();
    }

    // 2. QUERY NODES
    $now_formatted = (new DrupalDateTime('now'))->format('Y-m-d\TH:i:s');
    
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery();
    $nids = $query->condition('type', 'activiteit')
      ->condition('status', 1)
      ->condition('field_datum', $now_formatted, '>') 
      ->sort('field_datum', 'ASC')
      ->range(0, 5)
      ->accessCheck(TRUE)
      ->execute();

    $nodes = $storage->loadMultiple($nids);

    // 3. GENERATE LINKS
    $menu_weight = 0; 

    foreach ($nodes as $node) {
      $derivative_id = $node->id();
      
      $this->derivatives[$derivative_id] = $base_plugin_definition;
      $this->derivatives[$derivative_id]['title'] = $node->getTitle();
      $this->derivatives[$derivative_id]['route_name'] = 'entity.node.canonical';
      $this->derivatives[$derivative_id]['route_parameters'] = ['node' => $node->id()];
      $this->derivatives[$derivative_id]['menu_name'] = $menuName; 
      $this->derivatives[$derivative_id]['parent'] = $parentId;
      $this->derivatives[$derivative_id]['weight'] = $menu_weight++;
      
      // Ensure the menu rebuilds when this node changes
      $this->derivatives[$derivative_id]['cache_tags'] = $node->getCacheTags();
    }

    // Important: Add a general list tag so adding a NEW node triggers a refresh
    $this->derivatives['list_tag'] = $base_plugin_definition; 
    // This is a hacky way to attach metadata to the deriver, usually handled by cache invalidation elsewhere.
    // Instead, rely on standard cache tag invalidation 'node_list:activiteit'.

    return $this->derivatives;
  }
}