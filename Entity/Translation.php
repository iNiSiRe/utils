<?php

namespace PrivateDev\Utils\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrivateDev\Utils\Entity\TimestampEntityTrait;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Property;

/**
 * @Definition()
 *
 * @ORM\Table(name="translations")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Translation
{
    use TimestampEntityTrait;

    /**
     * @var int
     *
     * @Property()
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var []
     *
     * @Property()
     *
     * @ORM\Column(name="translation", type="json_array")
    */
    private $translation;

    /**
     * @var string
     *
     * @Property()
     *
     * @ORM\Column(name="entity_class", type="string")
     */
    private $entityClass;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $translation
     * @return Translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    public function getFieldTranslation($field, $language)
    {
        if (isset($this->translation[$language]) && isset($this->translation[$language][$field])) {
            return $this->translation[$language];
        } else {
            throw new \Exception(sprintf("translation %s to %s language not found for %s class", $field, $language, static::class));
        }
    }

    public function setFieldTranslation($field, $value, $language)
    {
        if (!isset($this->translation[$language])) {
            $this->translation[$language] = [];
        }

        $this->translation[$language][$field] = $value;

        return $this;
    }

    /**
     * @param string $entityClass
     * @return Translation
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

}
