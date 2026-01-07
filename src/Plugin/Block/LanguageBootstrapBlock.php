<?php

namespace Drupal\acdweb_tools\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Provides a 'Bootstrap Language Dropdown' block.
 *
 * @Block(
 * id = "acdweb_bootstrap_lang_dropdown",
 * admin_label = @Translation("Bootstrap Language Globe Dropdown"),
 * category = @Translation("ACD Web Tools")
 * )
 */
class LanguageBootstrapBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $languageManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, LanguageManagerInterface $language_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->languageManager = $language_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('language_manager')
    );
  }

  public function build() {
    // CHANGE: Use getNativeLanguages() instead of getLanguages()
    // This ensures "Nederlands" stays "Nederlands" even when viewing the English site.
    $languages = $this->languageManager->getNativeLanguages();
    $current_lang = $this->languageManager->getCurrentLanguage()->getId();
    $links = [];

    foreach ($languages as $lang_code => $language) {
      // Build the URL for the current page in the target language
      $url = Url::fromRoute('<current>', [], ['language' => $language]);
      
      $links[$lang_code] = [
        'name' => $language->getName(), 
        'url' => $url->toString(),
        'is_active' => ($lang_code === $current_lang),
      ];
    }

    return [
      '#theme' => 'language_dropdown', 
      '#links' => $links,
      '#attached' => [
        'library' => [
          'acdweb_tools/language-styling',
        ],
      ],
    ];
  }
}