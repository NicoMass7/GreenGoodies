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

  #[Route('/user', name: 'app_user')]
  public function index(): Response
  {
    return $this->render('user/index.html.twig', [
      'controller_name' => 'UserController',
    ]);
  }

  #[Route('/user/delete', name: 'delete')]
  public function delete(Security $security): Response
  {
    /** @var User $user */
    $user = $security->getUser();

    $basket = $user->getBasketProduct();
    if ($basket) {
      $this->entityManager->remove($basket);
    }

    foreach ($user->getOrders() as $order) {
      $this->entityManager->remove($order);
    }

    $this->entityManager->remove($user);
    $this->entityManager->flush();

    return $this->redirectToRoute('app_product_index');
  }

  #[Route('/addApiRights', name: 'addApiRights')]
  public function addApiRights(Security $security, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker): Response
  {
    /** @var User $user */
    $user = $security->getUser();

    $roles = $user->getRoles();
    if (!in_array('ROLE_API_READ', $roles)) {
      $roles[] = 'ROLE_API_READ';
      $user->setRoles($roles);
      $entityManager->persist($user);
      $entityManager->flush();
    }

    //Mettre à jour le token de l'utilisateur pour éviter la déconnexion
    $newToken = new UsernamePasswordToken($user, 'main', $roles);
    $userChecker->checkPreAuth($user); // Vérifie que l'utilisateur est toujours valide
    $tokenStorage->setToken($newToken);

    $this->addFlash('success', 'Accès API activé avec succès.');
    $orders = $this->orderRepository->findBy(['user' => $user]);

    return $this->render('order/index.html.twig', [
      'ordersList' => $orders,
    ]);
  }

  #[Route('/removeApiRights', name: 'removeApiRights')]
  public function removeApiRights(Security $security, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker): Response
  {
    /** @var User $user */
    $user = $security->getUser();

    $roles = $user->getRoles();
    if (in_array('ROLE_API_READ', $roles)) {
      $roles = array_diff($roles, ['ROLE_API_READ']);
      $user->setRoles($roles);
      $entityManager->persist($user);
      $entityManager->flush();
    }

    $newToken = new UsernamePasswordToken($user, 'main', $roles);
    $userChecker->checkPreAuth($user);
    $tokenStorage->setToken($newToken);

    $this->addFlash('success', 'Accès API révoqué avec succès.');
    $orders = $this->orderRepository->findBy(['user' => $user]);

    return $this->render('order/index.html.twig', [
      'ordersList' => $orders,
    ]);
  }
}
