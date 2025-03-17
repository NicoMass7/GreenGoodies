<?php

namespace App\Controller;

use App\Entity\BasketProduct;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/basket-product', name: 'app_basket_product_')]
final class BasketProductController extends AbstractController
{
  public function __construct(
    private BasketRepository $basketRepository,
    private ProductRepository $productRepository,
    private EntityManagerInterface $entityManager
  ) {}

  #[Route('/add/{productId}', name: 'add')]
  public function index(int $productId): Response
  {
    $userId = 17;

    $basket = $this->basketRepository->findOneBy(['user' => $userId]);
    $product = $this->productRepository->find($productId);

    // Vérifier si le produit est déjà dans le panier
    $basketProduct  = $this->entityManager->getRepository(BasketProduct::class)
      ->findOneBy(['basket' => $basket, 'product' => $product]);

    if ($basketProduct) {
      // Augmenter la quantité si le produit est déjà présent
      $basketProduct->setQuantity($basketProduct->getQuantity() + 1);
    } else {
      // Ajouter un nouveau produit au panier
      $basketProduct = new BasketProduct();
      $basketProduct->setBasket($basket);
      $basketProduct->setProduct($product);
      $basketProduct->setQuantity(1); // Par défaut, ajouter 1

      $this->entityManager->persist($basketProduct);
    }

    $this->entityManager->flush();

    return $this->redirect($this->generateUrl('app_product_show', ['id' => $productId]));
  }
}
