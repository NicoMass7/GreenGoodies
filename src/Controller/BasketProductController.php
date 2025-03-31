<?php

namespace App\Controller;

use App\Entity\BasketProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BasketProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/basket-product', name: 'app_basket_product_')]
final class BasketProductController extends AbstractController
{
  public function __construct(
    private BasketProductRepository $basketProductRepository,
    private ProductRepository $productRepository,
    private EntityManagerInterface $entityManager
  ) {}

  #[Route('/', name: 'show')]
  public function index(Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser();

    // Récupérer tous les objets BasketProduct associés au panier
    $basketProducts = $this->basketProductRepository->findBy(['user' => $user]);

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

    return $this->render('basket_product/index.html.twig', [
      'productsList' => $productsList,
      'totalPrice' => $totalPrice
    ]);
  }

  #[Route('/add/{productId}', name: 'add')]
  public function add(int $productId, Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser();

    $product = $this->productRepository->find($productId);

    // Vérifier si le produit est déjà dans le panier de l'utilisateur
    $basketProduct = $this->basketProductRepository->findOneBy(['user' => $user, 'product' => $product]);

    if ($basketProduct) {
      // Augmenter la quantité
      $basketProduct->setQuantity($basketProduct->getQuantity() + 1);
    } else {
      // Ajouter un nouveau produit au panier
      $basketProduct = new BasketProduct();
      $basketProduct->setUser($user);
      $basketProduct->setProduct($product);
      $basketProduct->setQuantity(1);

      $this->entityManager->persist($basketProduct);
    }

    $this->entityManager->flush();

    return $this->redirect($this->generateUrl('app_product_show', ['id' => $productId]));
  }

  #[Route('/delete', name: 'delete')]
  public function delete(Security $security): Response
  {
    $productsList = [];

    /** @var User $employe */
    $user = $security->getUser();

    $basketProducts = $this->basketProductRepository->findBy(['user' => $user]);

    foreach ($basketProducts as $basketProduct) {
      $this->entityManager->remove($basketProduct);
    }

    $this->entityManager->flush();

    return $this->render('basket_product/index.html.twig', ['productsList' => $productsList]);
  }
}
