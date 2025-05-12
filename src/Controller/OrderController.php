<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BasketProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order', name: 'app_order_')]
final class OrderController extends AbstractController
{
  public function __construct(
    private OrderRepository $orderRepository,
    private BasketProductRepository $basketProductRepository,
    private EntityManagerInterface $entityManager,
  ) {}

  // Route pour afficher les commandes de l'utilisateur connecté
  #[Route('/', name: 'show')]
  public function index(Security $security): Response
  {
    /** @var User $user */
    $user = $security->getUser(); // Récupère l'utilisateur connecté

    // Récupère toutes les commandes associées à cet utilisateur
    $orders = $this->orderRepository->findBy(['user' => $user]);

    // Affiche la page listant les commandes
    return $this->render('order/index.html.twig', [
      'ordersList' => $orders,
    ]);
  }

  // Route permettant de créer une nouvelle commande à partir du panier de l'utilisateur
  #[Route('/add', name: 'add')]
  public function add(Security $security): Response
  {
    /** @var User $user */
    $user = $security->getUser(); // Récupère l'utilisateur connecté

    // Récupère tous les produits actuellement dans le panier de l'utilisateur
    $basketProducts = $this->basketProductRepository->findBy(['user' => $user]);

    // Si aucun produit dans le panier, retourne une page d'erreur
    if (!$basketProducts) {
      return $this->render('exception/error404.html.twig');
    }

    // Calcule le prix total de la commande en multipliant prix x quantité pour chaque produit
    $totalPrice = 0;
    foreach ($basketProducts as $basketProduct) {
      $totalPrice += $basketProduct->getProduct()->getPrice() * $basketProduct->getQuantity();
    }

    // Récupère le dernier numéro de commande existant et incrémente pour en créer un nouveau
    $lastOrder = $this->orderRepository->findOneBy([], ['orderNumber' => 'DESC']);
    $newOrderNumber = $lastOrder ? $lastOrder->getOrderNumber() + 1 : 1;

    // Création et configuration d’un nouvel objet Order
    $order = new Order();
    $order->setUser($user);
    $order->setOrderNumber($newOrderNumber);
    $order->setValidationDate(new \DateTimeImmutable()); // Date actuelle
    $order->setTotalPrice($totalPrice);

    // Enregistre la commande dans la base de données
    $this->entityManager->persist($order);

    // Supprimer tous les produits du panier, car ils ont été commandés
    foreach ($basketProducts as $basketProduct) {
      $this->entityManager->remove($basketProduct);
    }

    // Valide les changements (sauvegarde)
    $this->entityManager->flush();

    // Redirige vers la page d'accueil des produits après la commande
    return $this->redirectToRoute('app_product_index');
  }
}
