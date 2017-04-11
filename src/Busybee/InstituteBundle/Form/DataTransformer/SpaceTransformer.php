<?php
namespace Busybee\InstituteBundle\Form\DataTransformer;

use Busybee\InstituteBundle\Entity\Space;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class SpaceTransformer implements DataTransformerInterface
{
    private $manager;

    /**
     * SpaceTransformer constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param mixed $data
     * @return mixed|string
     */
    public function transform($data)
    {
        if ($data instanceof Space)
			$data = strval($data->getId());
        else
            $data = '';
		return $data;
    }

    /**
     * @param mixed $data
     * @return Space|mixed|null|object
     */
    public function reverseTransform($data)
    {
        if (is_null($data))
			return null;
        if ($data instanceof Space)
            return $data ;
        $entity = $this->manager
            ->getRepository(Space::class)
            // query for the issue with this id
            ->find($data)
        ;

        if (is_null($entity)) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException('This message is 0verwritten by the validation message. ' . __FILE__ . __LINE__);
        }

        return $entity;
    }
}