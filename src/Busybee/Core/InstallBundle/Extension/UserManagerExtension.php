<?php

namespace Busybee\Core\InstallBundle\Extension;

use Symfony\Component\Form\FormInterface;

class UserManagerExtension extends \Twig_Extension
{
	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('get_userManager', array($this, 'getUserManager')),
			new \Twig_SimpleFunction('get_SystemYear', array($this, 'getSystemYear')),
		);
	}

	/**
	 * Main Twig extension. Call this in Twig to get formatted output of your form errors.
	 * Note that you have to provide form as Form object, not FormView.
	 *
	 * @param   FormInterface $form
	 * @param   string        $tag     The html tag, in which all errors will be packed. If you provide 'li',
	 *                                 'ul' wrapper will be added
	 * @param   string        $class   Class of each error. Default is none.
	 *
	 * @return  string
	 */
	public function getUserManager()
	{
		return null;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'user_manager_extension';
	}

	public function getSystemYear($user = null)
	{
		return new \DateTime('now');
	}
}