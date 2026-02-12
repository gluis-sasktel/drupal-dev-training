<?php

declare(strict_types=1);

namespace Drupal\audit\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent;
use Drupal\content_moderation\Entity\ContentModerationStateInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\audit\Event\IncidentReportEvents;
use Drupal\audit\Event\IncidentReport;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Logger\LoggerChannelFactory;

/**
 * @todo Add description for this subscriber.
 */
final class EntityDeletionSubscriber implements EventSubscriberInterface {

  protected AccountProxy $currentUser;

  protected EntityTypeManager $entityTypeManager;

  protected LoggerChannelFactory $logger;

  public function __construct(AccountProxy $currentUser, EntityTypeManager $entityTypeManager, LoggerChannelFactory $logger){
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
    $this->logger = $logger;
  }

  /**
   * Entity delete event handler.
   */
  public function logDeletion(EntityDeleteEvent $event): void {
    $deleted_entity = $event->getEntity();

    // Do nothing for config entities.
    if ($deleted_entity instanceof ConfigEntityInterface) {
      return ;
    }

    // Do nothing for content moderation state entities.
    if ($deleted_entity instanceof ContentModerationStateInterface) {
      return ;
    }

    $entity_type = $deleted_entity->getEntityTypeId();
    $bundle = $deleted_entity->bundle();
    $id = $deleted_entity->id();

    // Do nothing for path_aliases.
    if ($entity_type == 'path_alias') {
      return ;
    }

    // In all other cases create a DeletionRecord entity.
    $data = [
      'label' => $entity_type . ' - ' . $bundle . ' with ID = ' . $id . ' ' . $deleted_entity->label(),
      'deleted' => time(),
      'deleted_by' => $this->currentUser->id(),
      'entity_type' => $entity_type,
      'bundle' => $bundle,
    ];

    if (isset($deleted_entity->created)) {
      $data['created'] = $deleted_entity->created;
    }

    if (isset($deleted_entity->changed)) {
      $data['changed'] = $deleted_entity->changed;
    }

    if (isset($deleted_entity->uid)) {
      $data['deleted_entity_author'] = $deleted_entity->uid;
    }

    $record = $this->entityTypeManager->getStorage('deletion_record')->create($data);
    $record->save();
  }

  /**
   * If the new incident event is triggered, log it.
   */
  public function logIncident(IncidentReport $event) {
    $name = $event->getReporterName();
    $email = $event->getReporterEmail();
    $report = $event->getReport();
    $entity = $event->getEntity();

    //\Drupal::logger('audit')->alert("New incident reported by " . $name . " (" . $email . ") on entity " . $entity . ". Details: " . $report);
    $this->logger->get('audit')->alert("New incident reported by " . $name . " (" . $email . ") on entity " . $entity . ". Details: " . $report);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      EntityHookEvents::ENTITY_DELETE => ['logDeletion'],
      IncidentReportEvents::NEW_INCIDENT => ['logIncident'],
    ];
  }

}
