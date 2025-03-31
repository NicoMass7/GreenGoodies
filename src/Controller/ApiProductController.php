<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/products', name: 'api_products_')]
class ApiProductController extends AbstractController
{
  #[OA\Get(
    path: "/api/products",
    summary: "Récupère la liste des produits",
    tags: ["Produits"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Liste des produits",
        content: new OA\JsonContent(
          type: "array",
          items: new OA\Items(ref: "#/components/schemas/Product")
        )
      )
    ]
  )]
  #[Route('/', name: 'list', methods: ['GET'])]
  public function list(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
  {
    $products = $productRepository->findAll();
    $json = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }
}
