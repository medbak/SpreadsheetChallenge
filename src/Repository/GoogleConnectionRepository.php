<?php

namespace App\Repository;

use App\Interfaces\GoogleConnectionInterface;
use App\Interfaces\VerifyInterface;
use Google_Client;

class GoogleConnectionRepository implements GoogleConnectionInterface
{
    private $verify;

    public function __construct(VerifyInterface $verify)
    {
        $this->verify = $verify;
    }

    public function connect($pathToJson)
    {
        $message = '';

        if (!$this->verify->verifyFileExistence($pathToJson, $message) or !$this->verify->verifyFileType($pathToJson, 'json', $message)) {
            throw new \Exception($message);
        }

        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$pathToJson);

        // Create new client
        $client = new Google_Client();
        // Set credentials
        $client->useApplicationDefaultCredentials();

        return $client;
    }
}
