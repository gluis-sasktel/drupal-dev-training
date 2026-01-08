<?php

declare(strict_types=1);

namespace Drupal\helloworld\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use function kint;

/**
 * Returns responses for Hello World routes.
 */
final class HelloworldController extends ControllerBase {


  public function hello($name = NULL): array {

    $output = $this ->t('Hello to my World');

    if($name){
      $output = $this->t('Hello @name', ['@name' => $name]);
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $output,
    ];

    return $build;
  }

  /**
   * Builds the response for hello_name_node
   */
  public function helloNameNode($name, $node_id): array {

    $node = Node::load($node_id);

    //dpm($node);
    //dpm($node->title->value);
    //dpm($node->getTitle());
    //dpm($node->get('title')->value);
    
    if($node){
      $output = $this->t('Hello @name! <br> The title of the node is @title.', [
        '@name' => $name, 
        '@title' => $node->getTitle(), 
      ]);
    }else{
      $output = $this->t('Hello @name! <br> The node ID @id doesn\'t exist.', [
        '@name' => $name, 
        '@id' => $node_id, 
      ]);
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $output,
    ];

    return $build;

  }

}
