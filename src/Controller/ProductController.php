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

  #[Route('/', name: 'index')]
  public function index(): Response
  {
    $products = $this->productRepository->findALL();

    return $this->render('product/index.html.twig', [
      'products' => $products,
    ]);
  }

  #[Route('/show/{id}', name: 'show')]
  public function showProduct(int $id): Response
  {
    $product = $this->productRepository->find($id);

    if (!$product) {
      // Si le produit n'existe pas, retour en erreur
      return $this->render('exception/error404.html.twig');
    }

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
