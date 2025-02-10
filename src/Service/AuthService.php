<?php
// src/Service/LoginService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class AuthService
{
    private $client;
    private $apiUrl = 'http://identity_provider_container:8080/api/auth/';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function register(string $nom, string $prenom, string $email, string $dateNaissance, string $password): string
    {
        try {
            $response = $this->client->request('POST', $this->apiUrl . 'register', [
                'json' => [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'dateNaissance' => $dateNaissance,
                    'password' => $password
                ],
            ]);

            return $response->getContent();
        } catch (ClientExceptionInterface $e) {
            throw $e;
        }
    }  
}
?>
