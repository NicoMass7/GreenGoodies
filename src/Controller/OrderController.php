<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\BasketProduct;
use App\Repository\OrderRepository;
use App\Repository\BasketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order', name: 'app_order_')]
final class OrderController extends AbstractController
{
  public function __construct(
    private BasketRepository $basketRepository,
    private OrderRepository $orderRepository,
    private EntityManagerInterface $entityManager,
  ) {}

  #[Route('/', name: 'show')]
  public function index(Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser();

    $orders = $this->orderRepository->findBy(['user' => $user]);

    return $this->render('order/index.html.twig', [
      'ordersList' => $orders,
    ]);
  }

  #[Route('/add', name: 'add')]
  public function add(Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser();

    // Récupérer le panier de l'utilisateur
    $basket = $this->basketRepository->findOneBy(['user' => $user]);

    if (!$basket) {
      // Si l'utilisateur n'a pas de panier, retour en erreur
      return $this->render('exception/error404.html.twig');
    }

    $basketProducts = $this->entityManager->getRepository(BasketProduct::class)
      ->findBy(['basket' => $basket]);

    // Calcul du total de la commande
    $totalPrice = 0;
    foreach ($basketProducts as $basketProduct) {
      $totalPrice += $basketProduct->getProduct()->getPrice() * $basketProduct->getQuantity();
    }

    // Trouver le dernier numéro de commande et incrémenter
    $lastOrder = $this->orderRepository->findOneBy([], ['orderNumber' => 'DESC']);
    $newOrderNumber = $lastOrder ? $lastOrder->getOrderNumber() + 1 : 1;

    // Créer une nouvelle commande
    $order = new Order();
    $order->setUser($basket->getUser());
    $order->setOrderNumber($newOrderNumber);
    $order->setValidationDate(new \DateTimeImmutable());
    $order->setTotalPrice($totalPrice);

    $this->entityManager->persist($order);

    // Supprimer tous les produits du panier
    foreach ($basketProducts as $basketProduct) {
      $this->entityManager->remove($basketProduct);
    }

    $this->entityManager->flush();

    return $this->redirectToRoute('app_product_index');
  }
}
