<?php
namespace Busybee\StaffBundle\Form\DataTransformer;

use Busybee\StaffBundle\Entity\Staff;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class StaffTransformer implements DataTransformerInterface
{
    private $manager;

    /**
     * StaffTransformer constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transform an Object to a string
     *
     * @param  object $data Address
     * @return string
     */
    public function transform($data)
    {
        if ($data instanceof Staff)
			$data = strval($data->getId());
        else
            $data = '';
		return $data;
    }

    /**
     * Transforms a string to an Address Object
     *
     * @param  string $data
     * @return Object
     */
    public function reverseTransform($data)
    {
        if (empty($data))
			return null ;
        if ($data instanceof Staff)
            return $data ;

        $entity = $this->manager
            ->getRepository('BusybeePersonBundle:Staff')
            // query for the issue with this id
            ->find($data)
        ;

        if (is_null($entity)) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException('This message is overwritten by the validation message. ' . __FILE__ .': '. __LINE__);
        }

        return $entity;
    }
}