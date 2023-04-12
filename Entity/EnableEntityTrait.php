<?php

namespace PrivateDev\Utils\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EnableEntityTrait
 *
 * Notice: Don't forget add "@ORM\HasLifecycleCallbacks" to entity class
 *
 * @package Utils\Entity
 */
trait EnableEntityTrait
{
    /**
     * @var boolean
     */
    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: true)]
    private $enabled;

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}