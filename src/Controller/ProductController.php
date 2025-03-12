<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product', name: 'app_product')]
final class ProductController extends AbstractController
{
  #[Route('/', name: 'app_product')]
  public function index(): Response
  {
    return $this->render('product/index.html.twig', [
      'controller_name' => 'ProductController',
    ]);
  }

  #[Route('/show/{id}', name: 'app_product_show')]
  public function showProduct(): Response
  {
    return $this->render('product/show.html.twig', [
      'controller_name' => 'ProductController',
    ]);
  }

  #[Route('/add', name: 'app_product_add')]
  public function addProduct(): Response
  {
    return $this->render('product/add.html.twig', [
      'controller_name' => 'ProductController',
    ]);
  }
}
