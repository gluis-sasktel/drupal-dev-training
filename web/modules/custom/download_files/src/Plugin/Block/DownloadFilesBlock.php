<?php

declare(strict_types=1);

namespace Drupal\download_files\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Provides a download files block block.
 */
#[Block(
  id: 'download_files_block',
  admin_label: new TranslatableMarkup('Download files block'),
  category: new TranslatableMarkup('Custom'),
)]
final class DownloadFilesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $form = \Drupal::formBuilder()->getForm('\Drupal\download_files\Form\DownloadFilesForm');
    $form['#title'] = $this->t('Get your files here!');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $has_access = $account->hasPermission('download files');
    return AccessResult::allowedIf($has_access);
  }

}
