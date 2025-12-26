<?php

namespace Drupal\acdweb_tools\Plugin\Menu;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\menu_link_content\Entity\MenuLinkContent;

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
    // UUID of the parent 'Activiteiten' menu link
    $parentUuid = '51ed2590-3345-4271-935c-0d0f409d6325';
    $parentId = 'menu_link_content:' . $parentUuid;
    
    // Default to 'main', but try to find the REAL menu name from the parent
    $menuName = 'Activiteiten'; 
    
    // Load the parent entity to find out which menu it lives in
    $parent_entity = $this->entityTypeManager->getStorage('menu_link_content')->loadByProperties(['uuid' => $parentUuid]);
    if ($parent_entity) {
      $parent_entity = reset($parent_entity);
      $menuName = $parent_entity->getMenuName();
    }

    // 2. PREPARE DATE
    // Use standard ISO format. 
    // If your field is "Date only" (no time), change this to 'Y-m-d'
    $now = new DrupalDateTime('now');
    $now_formatted = $now->format('Y-m-d\TH:i:s');

    // 3. QUERY NODES
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery();
    
    $query->condition('type', 'activiteit')
      ->condition('status', 1)
      ->condition('field_datum', $now_formatted, '>') 
      ->sort('field_datum', 'ASC')
      ->range(0, 5)
      ->accessCheck(TRUE);

    $nids = $query->execute();
    $nodes = $storage->loadMultiple($nids);

    // 4. GENERATE LINKS
    foreach ($nodes as $node) {
      $nids = $query->execute();
    $nodes = $storage->loadMultiple($nids);

    // 1. START COUNTER HERE
    $menu_weight = 0; // <--- ADD THIS

    foreach ($nodes as $node) {
      $derivative_id = $node->id();
      
      $this->derivatives[$derivative_id] = $base_plugin_definition;
      $this->derivatives[$derivative_id]['title'] = $node->getTitle();
      $this->derivatives[$derivative_id]['route_name'] = 'entity.node.canonical';
      $this->derivatives[$derivative_id]['route_parameters'] = ['node' => $node->id()];
      $this->derivatives[$derivative_id]['menu_name'] = $menuName; 
      $this->derivatives[$derivative_id]['parent'] = $parentId;
      
      // 2. ASSIGN WEIGHT HERE
      $this->derivatives[$derivative_id]['weight'] = $menu_weight++; // <--- ADD THIS
      
      $this->derivatives[$derivative_id]['cache_tags'] = array_merge(
        $node->getCacheTags(),
        ['node_list:activiteit']
      );
    }

    return $this->derivatives;
  }
}
}