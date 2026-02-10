<?php

namespace Drupal\audit\Event;

/**
 * Defines events for incident reports.
 */
final class IncidentReportEvents {

  /**
   * Dispatched when a new incident is reported.
   * 
   * @Event
   * 
   * @see \Drupal\audit\Event\IncidentReport
   */
  const NEW_INCIDENT = 'audit.new_incident_report';
}