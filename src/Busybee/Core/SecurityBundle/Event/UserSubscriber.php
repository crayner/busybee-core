<?php

namespace Busybee\Core\SecurityBundle\Event;

use Busybee\Core\SecurityBundle\Form\DirectRoleType;
use Busybee\Core\SecurityBundle\Form\GroupType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class UserSubscriber implements EventSubscriberInterface
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * @var bool
	 */
	private $isSystemAdmin;

	/**
	 * UserSubscriber constructor.
	 *
	 * @param Session $session
	 * @param bool    $isSystemAdmin
	 */
	public function __construct(Session $session, $isSystemAdmin = false)
	{
		$this->session       = $session;
		$this->isSystemAdmin = $isSystemAdmin;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SUBMIT   => 'preSubmit',
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		if (empty($data['username']) && !empty($data['email']))
			$data['username'] = $data['email'];

		$data['usernameCanonical'] = $data['username'];
		$data['emailCanonical']    = $data['email'];

		if (trim(implode('', $data['expiresAt'])) !== '')
		{
			dump($data['expiresAt']);
		}
		if (trim(implode('', $data['credentialsExpireAt'])) !== '')
		{
			dump($data['credentialsExpireAt']);
		}

		if (is_null($event->getForm()->get('year')->getData()) || intval($data['year']) !== $event->getForm()->get('year')->getData()->getId())
			$this->session->clear('currentYear');

		$event->setData($data);
	}

	public function preSetData(FormEvent $event)
	{

		if ($this->isSystemAdmin)
		{
			$form = $event->getForm();
			$form
				->add('directroles', DirectRoleType::class)
				->add('groups', GroupType::class);
		}
	}
}