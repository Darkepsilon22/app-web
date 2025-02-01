<?php
// src/Service/LoginService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class LoginService
{
    private $client;
    private $apiUrl = 'http://localhost:8080/api/login/';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendPin(string $email,string $password): string
    {
        $response = $this->client->request('POST', $this->apiUrl . 'send', [
            'json' => ['email' => $email,'password' => $password,],
        ]);

        return $response->getContent();
    }

    public function verifyPin(string $email,string $password, int $pin): string
    {
        $response = $this->client->request('POST', $this->apiUrl . 'verify', [
            'query' => ['pin' => $pin],
            'json' => ['email' => $email,'password' => $password],
        ]);


        return $response->getContent();
    }
    public function resetSend(string $email,string $password): string
    {
        $response = $this->client->request('POST', $this->apiUrl . 'verify', [
            'json' => ['email' => $email,'password' => $password],
        ]);


        return $response->getContent();
    }

    
    
}
?>
