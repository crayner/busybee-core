<?php

namespace Busybee\RecordBundle\Model ;

use Symfony\Component\Yaml\Parser ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

abstract class ElementManager implements ElementManagerInterface
{
	protected $field;
	protected $table;
	protected $form;
	protected $container;
	private $data;
	protected $translator ;
	protected $changedRecord = false;
	private $validElement = false;
	
	public function __construct($rec_id, $field, $table, $form, Container $container)
	{
		$this->field = $field;
		$this->table = $table;
		$this->form = $form;
		$this->container = $container;
		$this->translator = $this->container->get('translator');
		$this->rec_id = $rec_id;
		return $this;
	}
	
	public function getElementValue($repo)
	{
		$entity = $repo->findOneBy(array(
					'record'		=> $this->rec_id,
					'field'			=> $this->field->getId(),
				)
			)
		;
		$value = NULL;
		if ($entity instanceof $this->class ){
			$value = $entity->getValue();
		} 
		if (empty($value))
			$value = $this->getParameter('default');
		return $value;
	}
	
	public function saveElement($record)
	{
		if (! $this->validateField())
			return $record;
		$data = $this->data;
		if ($record->getValue() != $data) {
			$record->setRecord($this->rec_id);
			$record->setTable($this->table->getId());
			$record->setField($this->field->getId());
			$record->setUser($this->container->get('security.token_storage')->getToken()->getUser()->getId());
			$record->setValue($data);
			$entityManager = $this->container->get('doctrine')->getManager();
			$entityManager->persist($record);
			$entityManager->flush();
			$this->setChangedRecord(true);
		}
		return $record;
	}
	
	protected function validateField()
	{
		$validators = $this->field->getValidator();
		$this->validElement = true;
		if (empty($validators))
			return $this->validElement;
		$yaml = new Parser();
		$validators = $yaml->parse($validators);
		$path = str_replace('/app', '/src/Busybee/RecordBundle/Validator/', str_replace('\\', '/', $this->container->getParameter('kernel.root_dir')));
		$this->validElement = true;
		foreach($validators as $validator => $constraints)
		{

			$vType = ucfirst(str_replace(array(' '), '_', $validator));
			$file = $path.$vType.'RecordValidator.php' ;

			if (is_file($file)) 
			{
				require_once $file;
				$class = '\\Busybee\\RecordBundle\\Validator\\'.$vType.'RecordValidator';
				$val = new $class($this->container, array( 'constraints' => $constraints, 'field' => $this->field, 'rec_id' => $this->rec_id ) );
				$this->setData($val->format($this->getData()));
				if (! $val->validate($this->getData()) ) {
					$this->form = $val->setErrorMessage($this->form, $this->table->getName(), $this->field->getName(), $this->getData());
					$this->validElement = false;
				}
			} else {
				throw new \InvalidArgumentException(sprintf('Crash and Burn for %s @ '.__FILE__.' '.__LINE__, $vType));
			}
		}
		return $this->validElement;
	}

	/**
     * Set Changed Record
     *
     * @param boolean
     *
     * @return Record
     */
    public function setChangedRecord($changedRecord)
    {
        $this->changedRecord = (bool)$changedRecord;

        return $this;
    }

    /**
     * Get Changed Record
     *
     * @return boolean
     */
    public function getChangedRecord()
    {
        return $this->changedRecord;
    }

    /**
     * Get Changed Record
     *
     * @return boolean
     */
    public function setValidElement($valid)
    {
        $this->validElement = (bool) $valid;
		return $this;
    }

    /**
     * Get Changed Record
     *
     * @return boolean
     */
    public function getValidElement()
    {
        return $this->validElement;
    }


	public function getForm()
	{
		return $this->form;
	}
	/**
	 * get Field Parameter
	 * @param 	string	Name of Parameter
	 * @return	mixed	null or parameter value
	 */
	public function getParameter($name)
	{
		$yaml = new Parser();
		$parameters = $yaml->parse($this->field->getParameters());
		if (isset($parameters[$name]))
			return $parameters[$name];
		return NULL;
	}
	
	protected function getHelp($settings)
	{
		if (empty($this->field->getHelp()))
			return $settings;
		$settings['help_block'] = $this->field->getFormattedHelp();
		return $settings;
	}
	protected function getPlaceholder($settings)
	{
		if (empty($this->getParameter('placeholder')))
			return $settings;
		$settings['placeholder'] = $this->getParameter('placeholder');
		return $settings;
	}
	
	public function getData()
	{
		return $this->data;
	}

	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}
}
