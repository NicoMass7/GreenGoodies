<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
   *
   * @todo inject services if required
   */
  public function __construct() {}

  public static function class(): string
  {
    return User::class;
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
   *
   * @todo add your default values here
   */
  protected function defaults(): array|callable
  {
    return [
      'firstName' => self::faker()->text(30),
      'lastName' => self::faker()->text(30),
      'mail' => self::faker()->email(),
      'password' => self::faker()->text(20),
    ];
  }

  /**
   * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
   */
  protected function initialize(): static
  {
    return $this
      // ->afterInstantiate(function(User $user): void {})
    ;
  }
}
