<?php

namespace Drupal\acdweb_tools\Controller;

use Drupal\Core\Controller\ControllerBase;

class AdminInfoController extends ControllerBase {

  public function content() {
    $config = $this->config('acdweb_tools.settings');
    $html_content = $config->get('admin_info_html');

    if (empty($html_content)) {
      return [
        '#markup' => $this->t('No content set. <a href=":url">Edit here</a>.', [
          ':url' => '/admin/config/services/acdweb-tools'
        ]),
      ];
    }

    return [
      '#prefix' => '<div class="admin-info-wrapper layout-container"><div class="gin-layer-wrapper">',
      '#suffix' => '</div></div>',
      '#type' => 'processed_text',
      '#text' => $html_content,
      '#format' => 'full_html',
    ];
  }
}