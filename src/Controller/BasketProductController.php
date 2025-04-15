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

  // Route pour afficher les produits du panier de l'utilisateur connecté
  #[Route('/', name: 'show')]
  public function index(Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser(); // Récupère l'utilisateur connecté

    // Récupère tous les objets BasketProduct (produits dans le panier) associés à cet utilisateur
    $basketProducts = $this->basketProductRepository->findBy(['user' => $user]);

    // Initialise la liste des produits à afficher et le prix total
    $productsList = [];
    $totalPrice = 0;

    // Parcourt chaque produit du panier pour préparer les données d'affichage
    foreach ($basketProducts as $basketProduct) {
      $product = $basketProduct->getProduct();
      $quantity = $basketProduct->getQuantity();
      $price = $product->getPrice() * $quantity;

      // Ajoute le produit à la liste avec ses détails
      $productsList[] = [
        'id' => $product->getId(),
        'name' => $product->getName(),
        'price' => $product->getPrice(),
        'quantity' => $basketProduct->getQuantity(),
        'image' => $product->getImage()
      ];
      // Calcule le prix total du panier
      $totalPrice += $price;
    }

    // Affiche la vue du panier avec les produits et le prix total
    return $this->render('basket_product/index.html.twig', [
      'productsList' => $productsList,
      'totalPrice' => $totalPrice
    ]);
  }

  // Route pour ajouter un produit au panier
  #[Route('/add/{productId}', name: 'add')]
  public function add(int $productId, Security $security): Response
  {
    /** @var User $employe */
    $user = $security->getUser(); // Récupère l'utilisateur connecté

    // Récupère le produit correspondant à l'ID
    $product = $this->productRepository->find($productId);

    // Vérifie si ce produit est déjà dans le panier
    $basketProduct = $this->basketProductRepository->findOneBy(['user' => $user, 'product' => $product]);

    if ($basketProduct) {
      // Si déjà présent, augmente la quantité de 1
      $basketProduct->setQuantity($basketProduct->getQuantity() + 1);
    } else {
      // Sinon, crée une nouvelle entrée panier pour ce produit
      $basketProduct = new BasketProduct();
      $basketProduct->setUser($user);
      $basketProduct->setProduct($product);
      $basketProduct->setQuantity(1);

      $this->entityManager->persist($basketProduct);
    }

    // Enregistre les modifications
    $this->entityManager->flush();

    // Redirige vers la page de détails du produit
    return $this->redirect($this->generateUrl('app_product_show', ['id' => $productId]));
  }

  // Route pour supprimer tous les produits du panier
  #[Route('/delete', name: 'delete')]
  public function delete(Security $security): Response
  {
    $productsList = [];

    /** @var User $employe */
    $user = $security->getUser(); // Récupère l'utilisateur connecté

    // Récupère tous les produits dans le panier de l'utilisateur
    $basketProducts = $this->basketProductRepository->findBy(['user' => $user]);

    // Supprime chaque élément du panier
    foreach ($basketProducts as $basketProduct) {
      $this->entityManager->remove($basketProduct);
    }

    // Valide les suppressions
    $this->entityManager->flush();

    // Recharge la vue panier vide
    return $this->render('basket_product/index.html.twig', ['productsList' => $productsList]);
  }
}
