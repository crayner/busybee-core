<?php

namespace Busybee\RecordBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

interface ElementManagerInterface
{
	/**
	 * @return form
	 */
	public function add();
	/**
	 * @return void
	 */
	public function save();

	public function getValue();

	public function __construct($rec_id, $field, $table, $form, Container $container);
	
}
