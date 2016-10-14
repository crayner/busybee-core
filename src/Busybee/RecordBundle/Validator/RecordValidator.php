<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

abstract class RecordValidator implements RecordValidatorInterface
{
	protected $container;
	protected $translator;
	private $constraints;
	private $message ;
	protected $field;
	protected $rec_id;

	public function __construct(Container $container, array $constraints)
	{
		$this->container = $container;
		$this->translator = $this->container->get('translator');
		$this->constraints = $constraints['constraints'];
		$this->field = $constraints['field'];
		$this->rec_id = $constraints['rec_id'];
		return $this;
	}

	public function get($name)
	{
		if (isset($this->constraints[$name]))
			return $this->constraints[$name];
		return NULL;
	}

	public function set($name, $value)
	{
		$this->constraints[$name] = $value;
	}
	
	protected function getMessage( $raw = 'record.not_valid.default', $parameters = array() )
	{
		$message = $this->getConstraints();
		if (is_array($message) and isset($message['message']))
			$raw = $message['message'];
		return $this->translator->trans($raw, $parameters, 'BusybeeRecordBundle');
	}
	
	protected function displayFieldName($name)
	{
		return str_replace(array('_'), ' ', $name);
	}
	
	public function getConstraints()
	{
		return $this->constraints;
	}
	
	public function format($data)
	{
		return $data;
	}
}