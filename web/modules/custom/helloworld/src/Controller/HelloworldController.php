<?php

declare(strict_types=1);

namespace Drupal\helloworld\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Hello World routes.
 */
final class HelloworldController extends ControllerBase {


  public function hello(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Custom module!'),
    ];

    return $build;
  }

}
