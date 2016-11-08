<?php

namespace PrivateDev\Utils\Test;

use MWL\ClientBundle\Security\Guard\JsonWebTokenAuthenticator;
use Symfony\Bundle\FrameworkBundle\Client as TestClient;

class Client
{
    /**
     * @var TestClient
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Client constructor.
     *
     * @param TestClient $client
     * @param string     $baseUrl
     */
    public function __construct(TestClient $client, $baseUrl = '/')
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param Request $request
     * @param bool    $jsonDecode
     *
     * @return array
     */
    public function request(Request $request, $jsonDecode = true)
    {
        if ($request->isPerformAuth()) {
            $headers = ['HTTP_' . JsonWebTokenAuthenticator::HEADER => $request->getAuthToken()];
        } else {
            $headers = [];
        }

        $this->client->request(
            $request->getMethod(),
            $this->baseUrl . $request->getUrl(),
            $request->getParameters(),
            [],
            $headers
        );

        $response = $this->client->getResponse();

        return [
            $jsonDecode === true ? json_decode($response->getContent(), true) : $response->getContent(),
            $response->getStatusCode(),
            $response->headers->all()
        ];
    }
}