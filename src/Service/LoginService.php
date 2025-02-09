<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginService
{
    private $client;
    private $apiUrl = 'http://localhost:8080/api/login/';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendPin(string $email, string $password): string
    {
        $response = $this->client->request('POST', $this->apiUrl . 'send', [
            'json' => ['email' => $email, 'password' => $password],
        ]);

        return $response->getContent();
    }

    public function verifyPin(string $email, string $password, int $pin): string
{
    $response = $this->client->request('POST', $this->apiUrl . 'verify?pin=' . $pin, [
        'json' => [
            'email' => $email,
            'password' => $password
        ],
    ]);

    $statusCode = $response->getStatusCode();
    $content = $response->getContent(false);

    // dump("Status Code: " . $statusCode);
    // dump("Response Content: " . $content);

    if ($statusCode !== 200) {
        throw new \Exception("Erreur API: " . $content);
    }

    // 🔹 Extraction du token depuis la réponse texte
    if (preg_match('/token généré : (\S+)/', $content, $matches)) {
        return $matches[1];  // ✅ Récupération du token
    }

    throw new \Exception("Token non trouvé dans la réponse.");
}


    public function resetSend(string $email, string $password): string
    {
        $response = $this->client->request('POST', 'http://localhost:8080/api/user/resettentative/send', [
            'json' => ['email' => $email, 'password' => $password],
        ]);

        return $response->getContent();
    }
}
