<?php

namespace App\Access\Normalizer;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use App\Access\EntityAccessBuilder;

/**
 * Entity access.
 */
class EntityNormalizer extends ObjectNormalizer
{
    private $discriminatorCache = [];

    protected $accessBuilder;

    public function __construct(EntityAccessBuilder $accessBuilder, ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null, ClassDiscriminatorResolverInterface $classDiscriminatorResolver = null, callable $objectClassResolver = null, array $defaultContext = [])
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor, $classDiscriminatorResolver, $objectClassResolver, $defaultContext);

        $this->accessBuilder = $accessBuilder;

        $this->defaultContext[self::CIRCULAR_REFERENCE_HANDLER] = function ($object, $format, $context) {
            return $this->accessBuilder->referenceAccess($object);
        };

        $this->defaultContext[self::MAX_DEPTH_HANDLER] = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return $this->accessBuilder->referenceAccess($innerObject);
        };
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->accessBuilder->isEntity($data);
    }

    /*
     * {@inheritdoc}
     *//*
    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        $value = parent::getAttributeValue($object, $attribute, $format, $context);

        try {
            $metadata = $this->entityManager->getClassMetadata(get_class($object));

            if ($metadata->hasAssociation($attribute)) {
                if ($metadata->isSingleValuedAssociation($attribute)) {
                    return $this->referenceAccess($value);
                } else if ($metadata->isCollectionValuedAssociation($attribute)) {
                    return $value->toArray();
                }
            } else {
                return $value;
            }
        } catch (\Exception $e) {
            return $value;
        }
    }*/
}
