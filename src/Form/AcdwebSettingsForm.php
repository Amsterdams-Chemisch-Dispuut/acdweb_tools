<?php

namespace Drupal\acdweb_tools\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class AcdwebSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return ['acdweb_tools.settings'];
  }

  public function getFormId() {
    return 'acdweb_tools_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('acdweb_tools.settings');

    $form['admin_info_html'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Admin Info HTML'),
      '#description' => $this->t('Enter raw HTML. Use with care.'),
      '#default_value' => $config->get('admin_info_html'),
      '#rows' => 20,
      '#attributes' => [
        'style' => 'font-family: monospace; background-color: #f4f4f4;',
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('acdweb_tools.settings')
      ->set('admin_info_html', $form_state->getValue('admin_info_html'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}