<?php

namespace PrivateDev\Utils\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notice: Don't forget add "@ORM\HasLifecycleCallbacks" to entity class
 */
trait TimestampEntityTrait
{
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private DateTime $updatedAt;

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function timestampCreatedAt()
    {
        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
    }

    #[ORM\PreUpdate]
    public function timestampUpdatedAt()
    {
        $this->setUpdatedAt(new DateTime());
    }
}