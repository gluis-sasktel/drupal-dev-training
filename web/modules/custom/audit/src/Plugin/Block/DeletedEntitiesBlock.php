<?php

declare(strict_types=1);

namespace Drupal\audit\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

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

    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
