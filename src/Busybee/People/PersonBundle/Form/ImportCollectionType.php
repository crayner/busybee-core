<?php

namespace Busybee\People\PersonBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ImportCollectionType extends CollectionType
{
	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'import_collection';
	}
}