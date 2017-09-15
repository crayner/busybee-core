<?php

namespace Busybee\Core\SecurityBundle\Security;

trait DenyAccessUnlessGranted
{
	/**
	 * Throws an exception unless the attributes are granted against the current authentication token and optionally
	 * supplied object.
	 *
	 * @param mixed  $attributes The attributes
	 * @param mixed  $object     The object
	 * @param string $message    The message passed to the exception
	 *
	 * @throws AccessDeniedException
	 */
	protected function denyAccessUnlessGranted($attributes, $object = null, $message = null)
	{
		$request   = $this->get('request_stack')->getCurrentRequest();
		$routeName = $request->get('_route');

		$page = $this->get('busybee_core_security.model.page_manager')->findOneByRoute($routeName, $attributes);

		$dev = $this->get('kernel')->getEnvironment();

		if ($dev === 'dev' && !is_string($this->get('busybee_core_security.model.get_current_user')))
			$message = is_null($message) ? $this->get('translator')->trans('security.access.denied.dev', ['%page%' => implode(', ', $page->getRoles()), '%user%' => $this->get('busybee_core_security.model.get_current_user')->rolesToString()], 'BusybeeSecurityBundle') : $message;
		else
			$message = is_null($message) ? $this->get('translator')->trans('security.access.denied.prod', [], 'BusybeeSecurityBundle') : $message;


		parent::denyAccessUnlessGranted($page->getRoles(), $object, $message);
	}
}
