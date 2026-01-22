<?php

declare(strict_types=1);

namespace Drupal\download_files\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Provides a Download files form.
 */
final class DownloadFilesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'download_files_download_files';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['media'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a file to download'),
      '#options' => $this->getFilesOptions(),
    ];

    $form['pass_phrase'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('Enter your email address to retrieve the file.'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    return $form;
  }

  public function getFilesOptions(){
    $config = \Drupal::config('download_files.settings');
    $types = $config->get('file_types');

    $results = \Drupal::database()
      ->select('file_managed', 'f')
      ->fields('f', ['filename', 'uri'])
      ->condition('f.status', 1)
      ->condition('filemime', $types, 'IN')
      ->execute()
      ->fetchAll();

    $options = [];
    foreach($results as $file){
      $options[$file->uri] = $file->filename;
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    //To keep the parten validations
    parent::validateForm($form, $form_state);

    //To add own validations
    $email = $form_state->getValue('pass_phrase');
    if(!strpos($email, '@mail.com')){
      $form_state->setErrorByName('pass_phrase', $this->t('Incorrect mail, try again!'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $uri = $form_state->getValue('media');
    $response = new BinaryFileResponse($uri);
    $response->setContentDisposition('attachment');
    $form_state->setResponse($response);
  }

}
