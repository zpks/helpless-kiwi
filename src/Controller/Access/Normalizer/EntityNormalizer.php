<?php

namespace App\Controller\Access\Normalizer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Entity access.
 */
class EntityNormalizer extends ObjectNormalizer
{
    private $discriminatorCache = [];

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, callable $objectClassResolver = null, array $defaultContext = [])
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

        $this->entityManager = $entityManager;

        $this->defaultContext[self::CIRCULAR_REFERENCE_HANDLER] = function ($object, $format, $context) {
            return $this->referenceAccess($object);
        };

        $this->defaultContext[self::MAX_DEPTH_HANDLER] = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $this->referenceAccess($innerObject);
        };
    }

    protected function entityIdentifier(object $entity)
    {
        $className = get_class($entity);
        $identifier = $this->entityManager->getClassMetadata($className)->getIdentifierValues($entity);

        // Find identifier value(s)
        if (1 == count($identifier)) {
            $identifier = reset($identifier);
        }

        return $identifier;
    }

    public function referenceAccess($entity): array
    {
        if (null == $entity || null == $this->entityManager->getClassMetadata($className)) {
            return [];
        }

        $className = get_class($entity);

        // Return reponse array
        return [
            'entity' => $className,
            'identifier' => $this->entityIdentifier($entity),
        ];
    }
}
