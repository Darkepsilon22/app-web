<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Entity\Users;
use App\Repository\CryptoUtilisateurRepository; // Assurez-vous que ce repository est bien importé
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'user_profile', methods:['GET'])]
    public function profile(int $id, ManagerRegistry $doctrine, CryptoUtilisateurRepository $cryptoUtilisateurRepository): Response
    {
        $repository = $doctrine->getRepository(Users::class);
        $user = $repository->find($id);
        $repositorycrypto = $doctrine->getRepository(Crypto::class);
        $cryptos = $repositorycrypto->findAll();

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $cryptoValues = [];

        foreach ($cryptos as $crypto) {
            $value = $crypto->getQuantiteCryptoUser($user->getIdUsers(), $cryptoUtilisateurRepository);
            $cryptoValues[] = [
                'crypto' => $crypto,
                'value' => $value
            ];
        }

        return $this->render('profile.html.twig', [
            'user' => $user,
            'cryptos' => $cryptoValues,
        ]);
    }
}
