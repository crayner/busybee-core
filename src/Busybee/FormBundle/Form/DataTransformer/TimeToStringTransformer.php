<?php
namespace Busybee\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use DateTime;
use DateTimeZone;

class TimeToStringTransformer implements DataTransformerInterface
{
	private $tz;
	
	public function __construct($tz)
	{
		$this->tz = $tz;
	}
    /**
     * Transforms an string to Time
     *
     * @param  Person|null $person
     * @return string
     */
    public function transform($data)
    {
		if ($data instanceof DateTime || empty($data)) return $data ;
		return new Datetime($data, new DateTimeZone($this->tz));
    }

    /**
     * Transforms a string (number) to a Time.
     *
     * @param  string $personNumber
     * @return Person|null
     * @throws TransformationFailedException if object (Person) is not found.
     */
    public function reverseTransform($data)
    {
		if (empty($data)) return $data ;

        if ($data instanceof DateTime)
            return $data;

        if (is_string($data))
            $data = new DateTime($data);

        return $data;
    }
}