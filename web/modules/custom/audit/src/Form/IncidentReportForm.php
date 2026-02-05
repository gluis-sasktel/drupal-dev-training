<?php

declare(strict_types=1);

namespace Drupal\audit\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Audit form.
 */
final class IncidentReportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'audit_incident_report';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['reporter_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Reporter name'),
      '#required' => TRUE,
      '#description' => $this->t('Type your name here.'),
    ];

    $form['reporter_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Reporter email'),
      '#required' => TRUE,
      '#description' => $this->t('Type your email address here.'),
    ];

    $form['entity'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the entity that was deleted incorrectly'),
      '#required' => TRUE,
      '#options' => $this->getEntities(),
    ];

    $form['report'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
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

  public function getEntities() {
    $storage = \Drupal::entityTypeManager()->getStorage('deletion_record');
    $query = $storage->getQuery();
    $query->sort('deleted', 'DESC');
    $query->accessCheck();
    $ids = $query->execute();

    $records = $storage->loadMultiple($ids);
    $options = [];
    foreach ($records as $key => $item) {
      $options[$key] = $item->label->value;
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
