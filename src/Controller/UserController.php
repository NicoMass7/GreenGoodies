<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

#[Route('/user', name: 'app_user_')]
final class UserController extends AbstractController
{
  public function __construct(
    private UserRepository $userRepository,
    private OrderRepository $orderRepository,
    private EntityManagerInterface $entityManager,
  ) {}

  // Route affichant la page d'accueil de l'espace utilisateur
  #[Route('/user', name: 'app_user')]
  public function index(): Response
  {
    // Affiche une page avec un simple message de bienvenue ou de test
    return $this->render('user/index.html.twig', [
      'controller_name' => 'UserController',
    ]);
  }

  // Route pour permettre à un utilisateur de supprimer son compte
  #[Route('/user/delete', name: 'delete')]
  public function delete(Security $security): Response
  {
    /** @var User $user */
    $user = $security->getUser(); // Récupère l'utilisateur actuellement connecté

    // Supprime le panier associé à l'utilisateur s’il existe
    $basket = $user->getBasketProduct();
    if ($basket) {
      $this->entityManager->remove($basket);
    }

    // Supprime toutes les commandes associées à l'utilisateur
    foreach ($user->getOrders() as $order) {
      $this->entityManager->remove($order);
    }

    // Supprime l'utilisateur
    $this->entityManager->remove($user);
    $this->entityManager->flush();

    // Redirige vers la liste des produits après suppression
    return $this->redirectToRoute('app_product_index');
  }

  // Route pour ajouter le rôle ROLE_API_READ à l'utilisateur connecté
  #[Route('/addApiRights', name: 'addApiRights')]
  public function addApiRights(Security $security, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker): Response
  {
    /** @var User $user */
    $user = $security->getUser();

    $roles = $user->getRoles();

    // Ajoute le rôle ROLE_API_READ s’il n’est pas déjà attribué
    if (!in_array('ROLE_API_READ', $roles)) {
      $roles[] = 'ROLE_API_READ';
      $user->setRoles($roles);
      $entityManager->persist($user);
      $entityManager->flush();
    }

    // Met à jour le token de sécurité de l'utilisateur pour prendre en compte ses nouveaux rôles
    $newToken = new UsernamePasswordToken($user, 'main', $roles);
    $userChecker->checkPreAuth($user); // Vérifie que l'utilisateur est toujours valide
    $tokenStorage->setToken($newToken);

    // Récupère les commandes de l'utilisateur pour les afficher
    $orders = $this->orderRepository->findBy(['user' => $user]);

    return $this->render('order/index.html.twig', [
      'ordersList' => $orders,
    ]);
  }

  // Route pour retirer le rôle ROLE_API_READ à l'utilisateur connecté
  #[Route('/removeApiRights', name: 'removeApiRights')]
  public function removeApiRights(Security $security, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker): Response
  {
    /** @var User $user */
    $user = $security->getUser();

    $roles = $user->getRoles();

    // Retire le rôle ROLE_API_READ si l'utilisateur l'a
    if (in_array('ROLE_API_READ', $roles)) {
      $roles = array_diff($roles, ['ROLE_API_READ']);
      $user->setRoles($roles);
      $entityManager->persist($user);
      $entityManager->flush();
    }

    // Met à jour le token de sécurité après modification des rôles
    $newToken = new UsernamePasswordToken($user, 'main', $roles);
    $userChecker->checkPreAuth($user);
    $tokenStorage->setToken($newToken);

    // Ajoute un message flash de confirmation
    $this->addFlash('success', 'Accès API révoqué avec succès.');

    // Récupère les commandes de l'utilisateur pour les afficher
    $orders = $this->orderRepository->findBy(['user' => $user]);

    return $this->render('order/index.html.twig', [
      'ordersList' => $orders,
    ]);
  }
}
