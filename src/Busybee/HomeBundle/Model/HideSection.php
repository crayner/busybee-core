<?php

namespace Busybee\HomeBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class HideSection
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $route;

    /**
     * HideSection constructor.
     * @param Session $session
     * @param RequestStack $request
     */
    public function __construct(Session $session, RequestStack $request)
    {
        $this->session = $session;
        $this->route = $request->getCurrentRequest()->get('_route');
    }

    /**
     * Hide Section On
     * @param string $route
     */
    public function hideSectionOn($route = null)
    {
        $hs = $this->session->get('hideSection');

        if (!is_null($route))
            $this->route = $route;

        if (empty($hs))
            $hs = [];

        $hs[$this->route] = true;

        $this->session->set('hideSection', $hs);
    }

    /**
     * Hide Section Off
     *
     * @param string $route
     */
    public function hideSectionOff($route = null)
    {
        $hs = $this->session->get('hideSection');

        if (!is_null($route))
            $this->route = $route;

        if (empty($hs))
            $hs = [];

        if (isset($hs[$this->route]))
            unset($hs[$this->route]);

        $this->session->set('hideSection', $hs);
    }
}