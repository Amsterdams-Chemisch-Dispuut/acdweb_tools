<?php

namespace Drupal\acdweb_tools\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Datetime\DrupalDateTime;

class EventsJsonController extends ControllerBase {

  public function handle() {
    $events = [];
    
    // Set the target timezone (Amsterdam)
    $amsterdam_tz = new \DateTimeZone('Europe/Amsterdam');

    $query = \Drupal::entityQuery('node')
      ->accessCheck(TRUE)
      ->condition('type', 'activiteit')
      ->condition('status', 1); // <--- THIS ENSURES ONLY PUBLISHED EVENTS SHOW
    
    // Optional: Sort by date so JSON is easier to read (descending or ascending)
    $query->sort('field_datum', 'ASC');

    $nids = $query->execute();
    
    if (!empty($nids)) {
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

      foreach ($nodes as $node) {
        // Skip if date field is empty
        if ($node->get('field_datum')->isEmpty()) {
          continue;
        }

        // 1. Get values (Drupal stores these as UTC strings)
        $start_value = $node->get('field_datum')->value;
        $end_value   = $node->get('field_datum')->end_value;

        // 2. Create Date Objects in UTC first
        $start_date = new DrupalDateTime($start_value, 'UTC');
        $end_date   = new DrupalDateTime($end_value, 'UTC');

        // 3. Convert to Amsterdam Time
        $start_date->setTimezone($amsterdam_tz);
        $end_date->setTimezone($amsterdam_tz);

        $events[] = [
          'id'    => $node->id(),
          'title' => $node->getTitle(),
          // Format with Offset (e.g., +01:00) so JS handles timezone correctly
          'start' => $start_date->format('Y-m-d\TH:i:sP'),
          'end'   => $end_date->format('Y-m-d\TH:i:sP'),
          'url'   => $node->toUrl()->toString(),
        ];
      }
    }

    return new JsonResponse($events);
  }
}