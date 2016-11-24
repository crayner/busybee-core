<?php
namespace Busybee\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class YesNoTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (person) to a string (number).
     *
     * @param  Person|null $person
     * @return string
     */
    public function transform($data)
    {
        return $data === 'Y' ? true : false;
    }

    /**
     * Transforms a string (number) to an object (person).
     *
     * @param  string $personNumber
     * @return Person|null
     * @throws TransformationFailedException if object (Person) is not found.
     */
    public function reverseTransform($data)
    {
       return $data ? 'Y' : 'N';
    }
}