<?php

namespace Busybee\Core\SecurityBundle\Model;

use Busybee\Core\SecurityBundle\Doctrine\UserManager;
use Symfony\Component\Form\FormInterface;


class UserManagerExtension extends \Twig_Extension
{
	/**
	 * @var FormErrorsParser
	 */
	private $parser;

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * FormErrorsExtension constructor.
	 *
	 * @param \Busybee\Core\FormBundle\Model\FormErrorsParser $parser
	 * @param                                                 $trans
	 *
	 * @throws \Exception
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}

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
		return $this->userManager;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'user_manager_extension';
	}

	public function getSystemYear(UserInterface $user)
	{
		return $this->userManager->getSystemYear($user);
	}
}