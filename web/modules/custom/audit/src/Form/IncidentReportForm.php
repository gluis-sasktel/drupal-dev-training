<?php

declare(strict_types=1);

namespace Drupal\audit\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\audit\Event\IncidentReport;
use Drupal\audit\Event\IncidentReportEvents;
Use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManager;
Use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Provides a Audit form.
 */
final class IncidentReportForm extends FormBase {

  protected EntityTypeManager $entityTypeManager;

  protected EventDispatcher $dispatcher;

  public static function create(ContainerInterface $container){
    return new static(
      $container->get('event_type.manager'),
      $container->get('event_dispatcher'),
    );
  }

  public function __construct(EntityTypeManager $entityTypeManager, EventDispatcher $dispatcher){
    $this->entityTypeManager = $entityTypeManager;
    $this->dispatcher = $dispatcher;
  }


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
    //$storage = \Drupal::entityTypeManager()->getStorage('deletion_record');
    $storage = $this->entityTypeManager->getStorage('deletion_record');
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
    if (mb_strlen($form_state->getValue('report')) < 15) {
      $form_state->setErrorByName(
        'report',
        $this->t('The report should be at least 15 characters.'),
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $reporterName = $form_state->getValue('reporter_name');
    $reporterEmail = $form_state->getValue('reporter_email');
    $entity = $form_state->getValue('entity');
    $report = $form_state->getValue('report');

    $eventObject = new IncidentReport($reporterName, $reporterEmail, $entity, $report);        
    //$event_dispatcher->dispatch($eventObject, IncidentReportEvents::NEW_INCIDENT);
    $this->dispatcher->dispatch($eventObject, IncidentReportEvents::NEW_INCIDENT);

    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}