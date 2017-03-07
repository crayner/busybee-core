<?php
namespace Busybee\InstituteBundle\Form\DataTransformer;

use Busybee\InstituteBundle\Entity\Year ;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class YearTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transform an Object to a string
     *
     * @param  object $data Year
     * @return string
     */
    public function transform($data)
    {
		if ($data instanceof Year)
			return strval($data->getId());
        return '0';
    }

    /**
     * Transforms a string to an Address Object
     *
     * @param  string $data
     * @return Object
     */
    public function reverseTransform($data)
    {
        if (is_null($data) || empty($data))
			return null;
		if (is_string($data) && $data === 'Add')
            return '0';

		if ($data instanceof Year)
			return $data;

        if (is_string($data) && strpos($data, 'calendar_year_') !== false)
            return null;

        $entity = $this->manager
            ->getRepository(Year::class)
            // query for the issue with this id
            ->find($data)
        ;

        if (is_null($entity)) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException('This message is overwritten by the validation message. ' . __FILE__ . __LINE__);
        }

        return $entity;
    }
}