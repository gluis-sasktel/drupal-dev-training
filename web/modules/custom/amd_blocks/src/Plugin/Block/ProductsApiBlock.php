<?php

declare(strict_types=1);

namespace Drupal\amd_blocks\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a products api block.
 */
#[Block(
  id: 'amd_blocks_products_api',
  admin_label: new TranslatableMarkup('Products API'),
  category: new TranslatableMarkup('Custom'),
)]
final class ProductsApiBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ClientInterface $httpClient,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    
    $response = $httpClient->request('GET', 'https://dummyjson.com/products');
    if ($response->getStatusCode() == 200){
      $data = $response->getBody()->getContents();

      $product = array_pop($data->products);

      $build['product_'.$product->id] = [
        '#theme' => 'featured_product',
        '#title' => $product->title,
        '#price' => $product->price,
      ];
    }
    
  
    return $build;
  }

}
