<?php

declare(strict_types=1);

namespace Drupal\youtube_video_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'YouTube Video' formatter.
 */
#[FieldFormatter(
  id: 'youtube_video_formatter',
  label: new TranslatableMarkup('YouTube Video'),
  field_types: ['string'],
)]
class YoutubeVideoFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $setting = ['foo' => 'bar'];
    return $setting + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    return [
      $this->t('Foo: @foo', ['@foo' => $this->getSetting('foo')]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#theme' => 'youtube_video',
        '#video_id' => $item->value,
      ];
    }
    return $element;
  }

}
