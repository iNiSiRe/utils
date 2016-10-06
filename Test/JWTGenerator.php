<?php

namespace PrivateDev\Utils\Test;

use MWL\ClientBundle\Security\Guard\JsonWebTokenAuthenticator;

class JWTGenerator
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $internalIp;

    /**
     * JWTGenerator constructor.
     *
     * @param string $serviceName
     * @param string $secret
     * @param string $internalIp
     */
    public function __construct(string $serviceName, string $secret, string $internalIp)
    {
        $this->serviceName = $serviceName;
        $this->secret = $secret;
        $this->internalIp = $internalIp;
    }

    /**
     * @param int    $user
     * @param array  $permissions
     * @param string $email
     *
     * @return string
     */
    public function generate(int $user, array $permissions = [], string $email = 'test@test.test') : string
    {
        return JsonWebTokenAuthenticator::dumpUserToken(
            $user,
            $email,
            [$this->serviceName => $permissions],
            $this->internalIp,
            $this->secret
        );
    }
}