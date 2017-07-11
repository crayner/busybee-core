<?php

namespace Busybee\SecurityBundle\Security;

use Busybee\SecurityBundle\Entity\Page;

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
        $request = $this->get('request_stack')->getCurrentRequest();
        $routeName = $request->get('_route');

        $page = $this->get('page.manager')->findOneByRoute($routeName, $attributes);

        $dev = $this->get('kernel')->getEnvironment();
        if ($dev === 'dev')
            $message = is_null($message) ? $this->get('translator')->trans('security.access.denied.dev', ['%page%' => implode(', ', $page->getRoles()), '%user%' => $this->get('grab.user.current')->rolesToString()], 'BusybeeSecurityBundle') : $message;
        else
            $message = is_null($message) ? $this->get('translator')->trans('security.access.denied.prod', [], 'BusybeeSecurityBundle') : $message;


        parent::denyAccessUnlessGranted($page->getRoles(), $object, $message);
    }
}
