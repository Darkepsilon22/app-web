<?php
// src/Service/LoginService.php
namespace App\Service;

use App\Entity\Users;
use App\Repository\TokenConnexionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenConnexionService
{

    public function getUserFromToken(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): ?Users
    {
        $token = $request->cookies->get('auth_token');

        if (!$token) {
            return null;
        }

        $user = $tokenRepository->findUserByToken($token);

        return $user;
    }
}
?>
