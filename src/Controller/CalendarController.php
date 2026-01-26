<?php

namespace Drupal\acdweb_tools\Controller;

use Drupal\Core\Controller\ControllerBase;

class CalendarController extends ControllerBase {

  public function content() {
    return [
      'calendar_container' => [
        '#type' => 'markup',
        '#markup' => '<div id="calendar"></div>',
      ],

      'calendar_footer' => [
        '#type' => 'markup',
        '#markup' => '
          <div class="btn-group w-100" role="group" aria-label="Add to calendar" style="margin-top: 20px;">
            <a href="#" class="btn btn-outline-primary flex-fill" id="google-calendar-btn" role="button">
              <i class="bi bi-google me-1"></i> ' . $this->t('Add to Google Calendar') . '
            </a>
          
            <a class="btn btn-outline-primary flex-fill"
               href="webcal://acdweb.nl/calendar.ics"
               target="_blank" rel="noopener noreferrer">
              <i class="bi bi-apple me-1"></i> ' . $this->t('Subscribe') . '
            </a>
          
            <a class="btn btn-outline-primary flex-fill"
               href="/calendar.ics"
               download="calendar.ics" rel="noopener">
              <i class="bi bi-download me-1"></i> ' . $this->t('Download .ics') . '
            </a>
          </div>',
      ],

      '#attached' => [
        'library' => [
          'acdweb_tools/calendar',
        ],
      ],
    ];
  }
}