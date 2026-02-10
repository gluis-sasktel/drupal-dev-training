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
   * Constructs an incident report event object.
   */
  public function __construct($reporterName) {
    $this->reporterName = $reporterName;
  }

  public function getReporterName() {
    return $this->reporterName;
  }
}