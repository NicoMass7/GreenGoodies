<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\BasketProduct;
use App\Repository\BasketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/basket', name: 'app_basket_')]
final class BasketController extends AbstractController
{
  public function __construct(
    private BasketRepository $basketRepository,
    private EntityManagerInterface $entityManager,
  ) {}

  #[Route('/', name: 'show')]
  public function index(Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser();

    $basket = $this->basketRepository->findOneBy(['user' => $user]);

    // Récupérer tous les objets BasketProduct associés au panier
    $basketProducts = $this->entityManager->getRepository(BasketProduct::class)
      ->findBy(['basket' => $basket]);

    // Extraire les produits de chaque BasketProduct et calculer le prix total
    $productsList = [];
    $totalPrice = 0;
    foreach ($basketProducts as $basketProduct) {
      $product = $basketProduct->getProduct();
      $quantity = $basketProduct->getQuantity();
      $price = $product->getPrice() * $quantity;

      $productsList[] = [
        'id' => $product->getId(),
        'name' => $product->getName(),
        'price' => $product->getPrice(),
        'quantity' => $basketProduct->getQuantity(),
        'image' => $product->getImage()
      ];
      $totalPrice += $price;
    }

    return $this->render('basket/index.html.twig', [
      'productsList' => $productsList,
      'totalPrice' => $totalPrice
    ]);
  }


  #[Route('/delete', name: 'delete')]
  public function delete(Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser();

    $basket = $this->basketRepository->findOneBy(['user' => $user]);

    $basketProducts = $this->entityManager->getRepository(BasketProduct::class)
      ->findBy(['basket' => $basket]);

    foreach ($basketProducts as $basketProduct) {
      $this->entityManager->remove($basketProduct);
    }

    $this->entityManager->flush();

    return $this->render('basket/index.html.twig', []);
  }
}
