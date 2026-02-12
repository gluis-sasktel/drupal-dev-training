<?php

namespace Drupal\audit\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Wraps an incident report event for event subscribers.
 */
class IncidentReport extends Event {
  /**
   * Reporter name.
   * 
   * @var string
   */
  protected $reporterName;

  /**
   * Reporter email.
   * 
   * @var string
   */
  protected $reporterEmail;

  /**
   * Incorrectly deleted entity.
   * 
   * @var string
   */
  protected $entity;

  /**
   * Report details.
   * 
   * @var string
   */
  protected $report;

  /**
   * Constructs an incident report event object.
   */
  public function __construct($reporterName, $reporterEmail, $entity, $report) {
    $this->reporterName = $reporterName;
    $this->reporterEmail = $reporterEmail;
    $this->entity = $entity;
    $this->report = $report;
  }

  public function getReporterName() {
    return $this->reporterName;
  }

  public function getReporterEmail() {
    return $this->reporterEmail;
  }

  public function getEntity() {
    return $this->entity;
  }

  public function getReport() {
    return $this->report;
  }
}