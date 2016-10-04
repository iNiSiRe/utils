<?php

namespace PrivateDev\Utils\Test;

use Symfony\Bundle\FrameworkBundle\Client as FrameworkClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestCase extends WebTestCase
{
    /**
     * @var FrameworkClient
     */
    protected $client;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Client
     */
    protected $apiClient;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();
        $this->apiClient = new Client($this->client, '/api/v1');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }
}