<?php

namespace Busybee\SystemBundle\Subscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;


class TablePrefixSubscriber implements \Doctrine\Common\EventSubscriber
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * TablePrefixSubscriber constructor.
     * @param $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName)
            $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping)
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY && $mapping['isOwningSide'])
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mapping['joinTable']['name'];
    }
}