<?php

declare(strict_types=1);

namespace Drupal\audit\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Cache\Cache;

/**
 * Provides a deleted entities block block.
 */
#[Block(
  id: 'audit_deleted_entities_block',
  admin_label: new TranslatableMarkup('Deleted Entities Block'),
  category: new TranslatableMarkup('Custom'),
)]
final class DeletedEntitiesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $storage = \Drupal::entityTypeManager()->getStorage('deletion_record');
    $query = $storage->getQuery();
    $query->sort('deleted', 'DESC');
    $query->accessCheck();
    $query->range(0, 3);
    $ids = $query->execute();

    $records = $storage->loadMultiple($ids);

    $output = '<h3>Recently deleted entities</h3><ol>';

    foreach ($records as $item) {
      $output = $output . '<li>' . $item->label->value . '</li>';
    }

    $output = $output . '</ol>';

    $build['content'] = [
      '#markup' => $output,
    ];
    return $build;
  }

  public function getCacheTags() {
    $tags = [
      'deletion_record_list',
    ];

    return Cache::mergeTags(parent::getCacheTags(), $tags);
  }

}
