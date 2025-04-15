<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController
{
  // Route pour afficher la page de login standard
  #[Route(path: '/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    // Si l'utilisateur est déjà connecté, on le redirige vers la page d'accueil des produits
    if ($this->getUser()) {
      return $this->redirectToRoute('app_product_index');
    }

    // Récupère la dernière erreur d'authentification (s'il y en a une)
    $error = $authenticationUtils->getLastAuthenticationError();

    // Récupère le dernier nom d'utilisateur saisi
    $lastUsername = $authenticationUtils->getLastUsername();

    // Rend le formulaire de connexion avec les infos précédentes
    return $this->render('security/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error,
    ]);
  }

  // Route de déconnexion – Symfony intercepte cette méthode automatiquement
  #[Route(path: '/logout', name: 'app_logout')]
  public function logout(): void
  {
    // Cette exception ne sera jamais levée : Symfony intercepte l'appel
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  // Route d'authentification pour l'API – génère un token JWT si les identifiants sont valides
  #[Route('/api/login', name: 'api_login', methods: ['POST'])]
  #[OA\Post(
    path: "/api/login",
    summary: "Connecte un utilisateur et retourne un token JWT",
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        type: "object",
        required: ["username", "password"],
        properties: [
          new OA\Property(property: "username", type: "string"),
          new OA\Property(property: "password", type: "string")
        ]
      )
    ),
    responses: [
      new OA\Response(
        response: 200,
        description: "Retourne un token JWT",
        content: new OA\JsonContent(
          type: "object",
          properties: [
            new OA\Property(property: "token", type: "string")
          ]
        )
      ),
      new OA\Response(
        response: 401,
        description: "Identifiants invalides"
      )
    ]
  )]
  public function apiLogin(#[CurrentUser] ?UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
  {
    // Si l'utilisateur n'est pas authentifié ou n'a pas le rôle adéquat, on refuse l'accès
    if (!$user || !in_array('ROLE_API_READ', $user->getRoles())) {
      return $this->json(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    // Génère un token JWT à partir de l'utilisateur connecté
    $token = $JWTManager->create($user);

    // Retourne le token et des infos de l'utilisateur
    return $this->json([
      'token' => $token,
      'user'  => $user->getUserIdentifier(),
      'roles' => $user->getRoles(),
    ]);
  }
}
