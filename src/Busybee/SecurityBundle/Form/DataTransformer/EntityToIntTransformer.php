<?php 
 
namespace Busybee\SecurityBundle\Form\DataTransformer ;
 
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
 
class EntityToIntTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;
    private $entityClass;
    private $entityType;
    private $entityRepository;
 
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $entityClass)
    {
        $this->om = $om;
        $this->setEntityClass($entityClass);
    }
 
    /**
     * @param mixed $entity
     *
     * @return integer
     */
    public function transform($entity)
    {
        if (null === $entity || ! $entity instanceof $this->entityClass) {
            return 0;
        }
 
        return $entity->getId();
    }
 
    /**
     * @param mixed $id
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed|object
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }
 
        $entity = $this->entityRepository->find($id);
 
        if (null === $entity) {
            throw new TransformationFailedException(
                sprintf(
                    'A %s with id "%s" does not exist!',
                    $this->entityType,
                    $id
                )
            );
        }
 
        return $entity;
    }
 
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        $this->setEntityRepository($entityClass);
    }

    public function setEntityRepository($entityClass)
    {
        $this->entityRepository = $this->om->getRepository($entityClass);
    }
}