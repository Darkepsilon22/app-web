<?php
// src/Service/LoginService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class LoginService
{
    private $client;
    private $apiUrl = 'http://identity_provider_container:8080/api/login/';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendPin(string $email, string $password): string
    {
        try {
            $response = $this->client->request('POST', $this->apiUrl . 'send', [
                'json' => ['email' => $email, 'password' => $password],
            ]);

            return $response->getContent();
        } catch (ClientExceptionInterface $e) {
            throw $e;
        }
    }

    public function verifyPin(string $email,string $password, int $pin): string
    {
        try {
            $response = $this->client->request('POST', $this->apiUrl . 'verify', [
                'query' => ['pin' => $pin],
                'json' => ['email' => $email,'password' => $password],
            ]);
            return $response->getContent();
        } catch (ClientExceptionInterface $e) {
            throw $e;
        }
    }
    public function resetSend(string $email,string $password): string
    {
        try {
            $response = $this->client->request('POST', 'http://identity_provider_container:8080/api/user/resettentative/send', [
                'json' => ['email' => $email,'password' => $password],
            ]);
            return $response->getContent();
        } catch (ClientExceptionInterface $e) {
            throw $e;
        }
    }   
}
?>
