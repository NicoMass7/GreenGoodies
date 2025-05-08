<?php

namespace App\Factory;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Product>
 */
final class ProductFactory extends PersistentProxyObjectFactory
{
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   *
   * @todo inject services if required
   */
  public function __construct() {}

  public static function class(): string
  {
    return Product::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   *
   * @todo add your default values here
   */
  protected function defaults(): array|callable
  {
    $faker = self::faker();

    $plats = [
      [
        'court' => 'Un plat réconfortant aux saveurs provençales.',
        'long' => 'Ce plat mijoté associe tomates confites, aubergines fondantes et herbes de Provence, 
                       pour une expérience gustative authentique et généreuse.'
      ],
      [
        'court' => 'Un curry doux et parfumé pour les amateurs d’épices.',
        'long' => 'Ce curry végétalien est préparé avec du lait de coco, des pois chiches, et des légumes colorés. 
                       Parfait pour un déjeuner exotique et équilibré.'
      ],
      [
        'court' => 'Un risotto crémeux à savourer sans modération.',
        'long' => 'Ce risotto aux champignons, relevé d’une touche de parmesan et de vin blanc, 
                       est un classique de la cuisine italienne revisité avec passion.'
      ],
    ];

    $plat = $faker->randomElement($plats);

    return [
      'image' => self::getRandomImage(),
      'name' => self::faker()->text(20),
      'price' => self::faker()->randomFloat(2, 1, 100),
      'shortDescription' => $plat['court'],
      'longDescription' => $plat['long'],
    ];
  }

  // Fonction de récupération d'une image aléatoire
  private static function getRandomImage(): string
  {
    // Définit le chemin absolu du dossier contenant les images des produits
    $uploadsDir = __DIR__ . '/../../public/uploads/products';

    // Scanne le dossier pour récupérer tous les fichiers, en excluant "." et ".." (répertoires spéciaux),
    // puis réindexe le tableau pour éviter des clés non séquentielles
    $files = array_values(array_diff(scandir($uploadsDir), ['.', '..']));

    // Vérifie si des fichiers existent dans le dossier :
    // - Si oui, sélectionne un fichier au hasard et le retourne
    // - Sinon, retourne 'default.jpg' (une image par défaut)
    return !empty($files) ? $files[array_rand($files)] : 'default.jpg';
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static
  {
    return $this
      // ->afterInstantiate(function(Product $product): void {})
    ;
  }
}
