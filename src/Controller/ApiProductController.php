<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Route de base pour toutes les routes de ce contrôleur : /api/products
// Accès restreint aux utilisateurs ayant le rôle ROLE_API_READ
#[Route('/api/products', name: 'api_products_')]
#[IsGranted('ROLE_API_READ')]
class ApiProductController extends AbstractController
{
  // Documentation OpenAPI (Swagger) pour la route GET /api/products
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
  // Route GET /api/products/
  #[Route('/', name: 'list', methods: ['GET'])]
  public function list(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
  {
    // Récupère tous les produits depuis la base de données
    $products = $productRepository->findAll();

    // Sérialise les produits en JSON en respectant le groupe 'product:read'
    $json = $serializer->serialize($products, 'json', ['groups' => 'product:read']);

    // Retourne la réponse JSON avec le code 200 (OK)
    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }
}
