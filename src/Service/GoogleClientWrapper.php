<?php
declare(strict_types=1);

namespace App\Service;

/**
 * Class GoogleClientWrapper
 * Simple wrapper for Google_Client
 * @package App\Service
 */
class GoogleClientWrapper
{
    /** @var  \Google_Client */
    private $googleClient;

    public function __construct(string $googleClient)
    {
        $this->googleClient = new \Google_Client(['client_id' => $googleClient]);
    }

    public function verifyIdToken(string $idToken)
    {
        return $this->googleClient->verifyIdToken($idToken);
    }
}