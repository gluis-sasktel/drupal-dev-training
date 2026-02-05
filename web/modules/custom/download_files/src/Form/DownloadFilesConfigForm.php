<?php

declare(strict_types=1);

namespace Drupal\download_files\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Download files settings for this site.
 */
final class DownloadFilesConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'download_files_download_files_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['download_files.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['file_types'] = [
      '#type' => 'checkboxes',
      '#options' => $this->getFileTypes(),
      '#title' => $this->t('File types to include in the download files form'),
      '#default_value' => $this->config('download_files.settings')->get('file_types'),
    ];
    return parent::buildForm($form, $form_state);
  }

  public function getFileTypes() {
    $results = \Drupal::database()
      ->select('file_managed', 'f')
      ->distinct()
      ->fields('f', ['filemime'])
      ->execute()
      ->fetchCol();

    $types = [];
    foreach ($results as $type) {
      $types[$type] = $type;
    }
    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if ($form_state->getValue('example') === 'wrong') {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('The value is not correct.'),
    //     );
    //   }
    // @endcode
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('download_files.settings')
      ->set('file_types', $form_state->getValue('file_types'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
