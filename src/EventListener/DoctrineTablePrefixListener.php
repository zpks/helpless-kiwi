<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class DoctrineTablePrefixListener
{
    /**
     * @var ?string
     */
    protected $prefix = '';

    public function __construct(?string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $this->prefix.$classMetadata->getTableName(),
            ]);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'] && $mapping['isOwningSide'] === true) {
                $joinTable = (array) $mapping['joinTable'];
                $mappedTableName = $joinTable['name'];
                $mappingJoinTable = (array) $classMetadata->associationMappings[$fieldName]['joinTable'];
                $mappingJoinTable['name'] = $this->prefix.$mappedTableName;
            }
        }
    }
}
