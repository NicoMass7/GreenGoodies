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
    return [
      'image' => self::getRandomImage(),
      'longDescription' => self::faker()->text(),
      'name' => self::faker()->text(20),
      'price' => self::faker()->randomFloat(2, 1, 100),
      'shortDescription' => self::faker()->text(50),
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
