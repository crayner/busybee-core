<?php
namespace Busybee\InstituteBundle\Form\DataTransformer;

use Busybee\InstituteBundle\Entity\CampusResource;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class CampusResourceTransformer implements DataTransformerInterface
{
    private $manager;

    /**
     * CampusTransformer constructor.
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
        if ($data instanceof CampusResource)
			$data = strval($data->getId());
        else
            $data = '';
		return $data;
    }

    /**
     * @param mixed $data
     * @return CampusResource|mixed|null|object
     */
    public function reverseTransform($data)
    {
        if (is_null($data))
			return null;
        if ($data instanceof CampusResource)
            return $data ;
        $entity = $this->manager
            ->getRepository('BusybeeInstituteBundle:CampusResource')
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