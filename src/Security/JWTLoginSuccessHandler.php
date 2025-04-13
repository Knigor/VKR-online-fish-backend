<?php

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

class JWTLoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface $entityManager
    ) {
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        /** @var Users $user */
        $user = $token->getUser();

        if (!$user instanceof Users) {
            throw new \LogicException('Authenticated user is not an instance of Users.');
        }

        // Генерация токенов
        $accessToken = $this->jwtManager->create($user);

        // Генерация refresh token вручную (если используешь refresh-токен менеджер — подставь его)
        $refreshTokenPayload = [
            'username' => $user->getUserIdentifier(),
            'exp' => time() + 60 * 60 * 24 * 7 // 7 дней
        ];
        $refreshToken = base64_encode(json_encode($refreshTokenPayload)); // Лучше использовать отдельный сервис или Lexik

        // Создание HttpOnly cookie с refresh_token
        $refreshTokenCookie = Cookie::create('refresh_token')
            ->withValue($refreshToken)
            ->withHttpOnly(true)
            ->withSameSite('lax') // вместо 'none'
           // ->withDomain('localhost') // Явно указать
            ->withSecure(false)
            ->withPath('/')
            ->withExpires(strtotime('+7 days'));

        // JSON с access_token и данными
        $response = new JsonResponse([
            'access_token' => $accessToken,
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getNameUser(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'role' => $user->getRole()
            ]
        ]);

        // Установка cookie
        $response->headers->setCookie($refreshTokenCookie);

        return $response;
    }
}
