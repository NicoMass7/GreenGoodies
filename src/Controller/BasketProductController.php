<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BasketProductController extends AbstractController{
    #[Route('/basket/product', name: 'app_basket_product')]
    public function index(): Response
    {
        return $this->render('basket_product/index.html.twig', [
            'controller_name' => 'BasketProductController',
        ]);
    }
}
