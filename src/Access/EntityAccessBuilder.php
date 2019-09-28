<?php

namespace App\Access;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Entity access.
 */
class EntityAccessBuilder
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isEntity($object)
    {
        try {
            $className = get_class($object);

            return null != $this->entityManager->getClassMetadata($className);
        } catch (\Exception $e) {
            return false;
        }
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
        if (null == $entity || !$this->isEntity($entity)) {
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
