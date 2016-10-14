<?php

namespace Busybee\DatabaseBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Yaml\Dumper ;

class EnumeratorManager
{
	private $container ;
	private $context ;
	private $field ;
	private $table ;
	private $refresh ;
	private $logger ;
	
	public function __construct( Container $container)
	{
		$this->container = $container ;
		$this->context = array('File'=>__FILE__);
		$this->logger = $this->container->get('monolog.logger.busybee');

		return $this;
	}
	
	public function manageEnumerators($config, $refresh)
	{
		$this->refresh = $refresh ;
		$this->enumerators = array();
		foreach ($config as $w)
		{
			$name = $w['enumerator']['name'];
			$v = $w['enumerator']['list'];
			foreach( $v as $value =>  $prompt )
			{
				$enum = new \Busybee\DatabaseBundle\Entity\Enumerator();
				$enum->setName($name);
				$enum->setPrompt($prompt);
				$enum->setValue($value);
				$this->enumerators[] = $enum;
			}
		}
		return $this ;
	}

	public function saveEnumerators($changed)
	{
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		if (empty($this->enumerators))
			return ;
		$em = $this->container->get('doctrine')->getManager();
		$er = $this->container->get('enumerator.repository');
		$flush = false;
		foreach ( $this->enumerators as $q => $enum )
		{
			$save = false;
			$exists = $er->findOneBy(array('name' => $enum->getName(), 'value' => $enum->getValue()));
			if (! empty($exists)) 
			{
				if ($enum->getPrompt() != $exists->getPrompt())
				{
					$save = true;
					$exists->setPrompt($enum->getPrompt());
				}
				$enum = $exists;
			} 
			else 
			{
				$prompt = $enum->getPromptString();
				$exists = $er->findOneBy(array('name' => $enum->getName(), 'prompt' => $prompt));
				if (! empty($exists))
				{
					if ($enum->getValue() != $exists->getValue())
					{
						$save = true;
						$exists->setValue($enum->getValue());
					}
					$enum = $exists;
				}
			}
			if (($this->refresh and $save) or intval($enum->getID()) === 0)
			{
				$this->messages[] = array('status' => 'success', 'message' => $this->container->get('translator')->trans('database.load.saved.enumerator', array('%name%' => $enum->getName(),
					'%prompt%' => $enum->getValue(),
				), 'BusybeeDatabaseBundle'));
				$em->persist($enum);
				$flush = true;
				$context['LINE'] = intval(__LINE__) + 1;
				$this->logger->info(
					$this->container->get('translator')->trans(
						'database.load.saved.enumerator', 
						array(
							'%name%' => $enum->getName(),
							'%prompt%' => $enum->getValue(),
						),
						'BusybeeDatabaseBundle'),
					$context);
			}
			$this->enumerators[$q] = $enum;
		}
		if ($flush) {
			$em->flush();
			$changed = true;
		}
		return $changed;
	}
}