<?php

namespace PrivateDev\Utils\ORM\Blending;

use ReflectionClass;
use PaymentBundle\Entity\Transaction;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use PrivateDev\Utils\ORM\Annotations\Blendable;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Binarium\Core\Domain\Payment\Entity\TransactionExtra;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class MongoDBBlender implements BlenderInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var array
     */
    private $metadata = [];

    /**
     * MongoDBBlender constructor.
     *
     * @param DocumentManager           $dm
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(DocumentManager $dm, PropertyAccessorInterface $propertyAccessor)
    {
        $this->dm = $dm;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param array $objects
     *
     * @throws MongoDBException
     */
    public function blend(array $objects)
    {
        $objectsIndexedById = [];
        foreach ($objects as $object) {

            $className = get_class($object);

            $metadata = $this->getBlendableMetadataForClass($className);
            if (empty($metadata)) {
                continue;
            }

            $id = $this->propertyAccessor->getValue($object, $metadata['referencedColumnName']);
            if (!$id) {
                continue;
            }

            $objectsIndexedById[$className][$id] = $object;

        }

        foreach ($objectsIndexedById as $className => $objects) {

            $metadata = $this->getBlendableMetadataForClass($className);
            $referencedColumnValues = array_keys($objects);

            $documents = $this
                ->dm
                ->createQueryBuilder($metadata['targetDocument'])
                ->field($metadata['joinColumnName'])
                ->in($referencedColumnValues)
                ->getQuery()
                ->execute();

            /** @var TransactionExtra $document */
            foreach ($documents as $document) {

                $this
                    ->propertyAccessor
                    ->setValue(
                        $objectsIndexedById[$className][$this->propertyAccessor->getValue($document, $metadata['joinColumnName'])],
                        $metadata['fieldName'],
                        $document
                    );

            }

        }

    }

    /**
     * @param $className
     *
     * @return array
     */
    private function getBlendableMetadataForClass($className)
    {
        if (!isset($this->metadata[$className])) {

            $reflectionClass = new ReflectionClass(Transaction::class);
            $reader = new AnnotationReader();

            foreach ($reflectionClass->getProperties() as $property) {

                /** @var Blendable $blendable */
                $blendable = $reader->getPropertyAnnotation($property, Blendable::class);

                if ($blendable) {
                    $this->metadata[$className] = [
                        'fieldName'            => $property->getName(),
                        'targetDocument'       => $blendable->targetDocument,
                        'joinColumnName'       => $blendable->joinColumnName,
                        'referencedColumnName' => $blendable->referencedColumnName,
                    ];
                }

            }

        }

        return $this->metadata[$className];
    }
}