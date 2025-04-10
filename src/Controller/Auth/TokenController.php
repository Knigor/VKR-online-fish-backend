<?php

namespace App\Controller\Auth;

use App\Entity\Users;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class TokenController extends AbstractController
{
    #[Route('/api/token/refresh', name: 'api_token_refresh', methods: ['GET'])]
    public function refreshToken(Request $request, JWTTokenManagerInterface $jwtManager, EntityManagerInterface $em): JsonResponse
    {
        $refreshToken = $request->cookies->get('refresh_token');

        if (!$refreshToken) {
            return $this->json(['message' => 'Refresh token not found'], Response::HTTP_UNAUTHORIZED);
        }

        // Простейшая проверка (для примера)
        $decoded = json_decode(base64_decode($refreshToken), true);

        if (!$decoded || !isset($decoded['username']) || !isset($decoded['exp']) || $decoded['exp'] < time()) {
            return $this->json(['message' => 'Invalid or expired refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $em->getRepository(Users::class)->findOneBy(['email' => $decoded['username']]);
        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $accessToken = $jwtManager->create($user);

        return $this->json(['access_token' => $accessToken]);
    }


    #[Route('/api/logout', name: 'api_logout', methods: ['GET'])]
    public function logout(): JsonResponse
    {
        // Удаляем куку (устанавливаем с истекшим сроком)
        $response = new JsonResponse(['message' => 'Logged out']);
        $expiredCookie = Cookie::create('refresh_token')
            ->withValue('')
            ->withExpires(time() - 3600)
            ->withHttpOnly(true)
            ->withPath('/')
            ->withSameSite('Strict');

        $response->headers->setCookie($expiredCookie);
        return $response;
    }

}
