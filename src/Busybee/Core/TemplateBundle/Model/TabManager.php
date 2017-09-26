<?php

namespace Busybee\Core\TemplateBundle\Model;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TabManager
{
	/**
	 * @var array
	 */
	private $tabs;

	/**
	 * @var array|ParameterBagInterface
	 */
	private $parameterBag;

	/**
	 * TabManager constructor.
	 *
	 * @param array $parameters
	 */
	public function __construct(ParameterBagInterface $parameterBag)
	{
		$this->parameterBag = $parameterBag;
	}

	/**
	 * @param string $tabName
	 */
	public function loadDefinition(string $tabName): array
	{
		$this->tabs = $this->parameterBag->get($tabName);

		if (empty($this->tabs))
			$this->tabs = [];

		return $this->getTabs();
	}

	/**
	 * @return array
	 */
	public function getTabs(): array
	{
		return $this->tabs;
	}


}