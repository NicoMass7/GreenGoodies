<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user', name: 'app_user_')]
final class UserController extends AbstractController
{
  public function __construct(
    private UserRepository $userRepository,
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

    $basket = $user->getBasket();
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
}
