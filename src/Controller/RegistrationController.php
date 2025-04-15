<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
  // Route qui gère l'inscription d’un nouvel utilisateur
  #[Route('/register', name: 'app_register')]
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
  {
    // Création d'une nouvelle instance de l'entité User
    $user = new User();

    // Création du formulaire d'inscription basé sur RegistrationFormType
    $form = $this->createForm(RegistrationFormType::class, $user);

    // Traitement de la requête HTTP (remplissage du formulaire)
    $form->handleRequest($request);

    // Vérifie si le formulaire a été soumis et est valide
    if ($form->isSubmitted() && $form->isValid()) {
      /** @var string $plainPassword */
      $plainPassword = $form->get('plainPassword')->getData();  // Récupération du mot de passe saisi en clair

      // Enregistre la date de création de l'utilisateur
      $user->setCreationDate(new \DateTimeImmutable());

      // Hashage et enregistrement sécurisé du mot de passe
      $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

      // Persistance et sauvegarde de l'utilisateur dans la base de données
      $entityManager->persist($user);
      $entityManager->flush();

      // Redirection vers la liste des produits après inscription réussie
      return $this->redirectToRoute('app_product_index');
    }

    // Affiche le formulaire d'inscription (en cas de 1ère visite ou d'erreurs de saisie)
    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form,
    ]);
  }
}
