<?php

namespace Busybee\Core\TemplateBundle\Extension;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionExtension extends \Twig_Extension
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * ButtonExtension constructor.
	 *
	 * @param Session $session
	 */
	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'session_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('hideSection', array($this, 'hideSection')),
		);
	}

	/**
	 * @param   string $value
	 *
	 * @return  int
	 */
	public function hideSection($route)
	{
		$hs = $this->session->get('hideSection');
		if (isset($hs[$route]))
			return $hs[$route];

		return false;
	}
}