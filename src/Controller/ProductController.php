<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product', name: 'app_product_')]
final class ProductController extends AbstractController
{
  public function __construct(
    private ProductRepository $productRepository,
    private EntityManagerInterface $entityManager,
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
  #[Route('/show/{id}', name: 'show')]
  public function showProduct(int $id): Response
  {
    // Récupère le produit avec l’ID donné
    $product = $this->productRepository->find($id);

    // Si aucun produit n’est trouvé, on affiche une page d’erreur personnalisée
    if (!$product) {
      return $this->render('exception/error404.html.twig');
    }

    // Sinon, on affiche la page de détails du produit
    return $this->render('product/show.html.twig', [
      'product' => $product,
    ]);
  }

  #[Route('/add', name: 'add')]
  public function addProduct(): Response
  {
    return $this->render('product/add.html.twig', [
      'controller_name' => 'ProductController',
    ]);
  }
}
