<?php

declare(strict_types=1);

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Returns responses for Hello World routes.
 */
final class HelloWorldController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function hello($name = NULL): array {
    $output = $this->t('Hello World!');

    // If there is no name, we may want to use the name of the currently logged in user.
    if (!$name) {
      $user = \Drupal::currentUser();
      // Check if the user is logged in and print its name. 
      // If the user is anonymous then we continue to print Hello World.
      if ($user->isAuthenticated()) {
        $name = $user->getDisplayName();
        $output = $this->t('Hello @name!', ['@name' => $name]);
      }
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $output,
    ];

    return $build;
  }

  /**
   * Builds the response for the hello_name_node route.
   */
  public function helloNameNode($name, $nid): array {
    if (!is_numeric($nid)) {
      return $build['content'] = [
        '#type' => 'item',
        '#markup' => $this->t('Hi @name! The ID should be a number.', ['@name' => $name]),
      ];
    }

    $node = Node::load($nid);
    // Print object.
    // dpm($node);

    // Get the title field object.
    // dpm($node->get('title'));

    // Print the title of the node.
    // dpm($node->title->value);
    // dpm($node->getTitle());
    // How to print values from complex fields.
    // dpm($node->field_brand->entity->name->value);

    if ($node) {
      // Get the link from the node directly.
      // $link = $node->toLink();

      // Build link manually using Url and Link classes.
      $url = Url::fromRoute('entity.node.canonical', ['node' => $nid]);
      $link = Link::fromTextAndUrl('node link', $url);

      $output = $this->t('Hello @name! The title of the node is @title.', [
        '@name' => $name,
        '@title' => $link->toString(),
      ]);
    } else {
      $output = $this->t('Hello @name! The node with the ID @id does not exist.', [
        '@name' => $name,
        '@id' => $nid,
      ]);
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $output,
    ];

    return $build;
  }

}
