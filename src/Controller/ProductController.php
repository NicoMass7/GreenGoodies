<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BasketProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', name: 'app_product_')]
final class ProductController extends AbstractController
{
  public function __construct(
    private ProductRepository $productRepository,
    private EntityManagerInterface $entityManager,
    private BasketProductRepository $basketProductRepository,
  ) {}

  // Route principale du site, affichant la liste des produits
  #[Route('/', name: 'index')]
  public function index(): Response
  {
    // Récupère tous les produits depuis le repository
    $products = $this->productRepository->findALL();

    // Affiche la vue avec la liste des produits
    return $this->render('product/index.html.twig', [
      'products' => $products,
    ]);
  }

  // Route qui affiche le détail d’un produit en fonction de son identifiant
  #[Route('/show_product/{id}', name: 'show')]
  public function showProduct(int $id, Security $security): Response
  {
    $product = $this->productRepository->find($id);
    if (!$product) {
      return $this->render('exception/error404.html.twig');
    }

    $basketProduct = null;
    $quantity = 1;
    $isInBasket = false;

    if ($security->getUser()) {
      $user = $security->getUser();
      $basketProduct = $this->basketProductRepository->findOneBy([
        'user' => $user,
        'product' => $product,
      ]);

      if ($basketProduct) {
        $quantity = $basketProduct->getQuantity();
        $isInBasket = true;
      }
    }

    return $this->render('product/show.html.twig', [
      'product' => $product,
      'quantity' => $quantity,
      'isInBasket' => $isInBasket
    ]);
  }

  #[Route('/add_product', name: 'add')]
  public function addProduct(): Response
  {
    return $this->render('product/add.html.twig', [
      'controller_name' => 'ProductController',
    ]);
  }
}
