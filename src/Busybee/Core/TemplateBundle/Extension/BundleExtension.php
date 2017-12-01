<?php

namespace Busybee\Core\TemplateBundle\Extension;

use Busybee\Core\SystemBundle\Model\BundleManager;

class BundleExtension extends \Twig_Extension
{
	/**
	 * @var BundleManager
	 */
	private $manager;

	/**
	 * ButtonExtension constructor.
	 *
	 * @param BundleManager $manager
	 */
	public function __construct(BundleManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'bundle_manager_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('sectionMenuTest', array($this->manager, 'sectionMenuTest')),
		);
	}
}