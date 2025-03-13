<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use App\Factory\ProductFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  public function __construct(
    private UserPasswordHasherInterface $hasher
  ) {}

  public function load(ObjectManager $manager): void
  {
    // Création d'un user pour la démo
    date_default_timezone_set('Europe/Paris');
    $now = new DateTimeImmutable();
    $user = new User();
    $user->setLastName('Masson')
      ->setFirstName('Nicolas')
      ->setMail('masson.nicolas@greengoodies.com')
      ->setPassword($this->hasher->hashPassword($user, 'NicolasMasson@1'))
      ->setCreationDate($now);
    $manager->persist($user);

    // Création de 11 produits pour la démo
    ProductFactory::createMany(11);

    $manager->flush();
  }
}
