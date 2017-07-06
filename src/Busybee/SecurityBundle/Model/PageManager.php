<?php

namespace Busybee\SecurityBundle\Model;

use Busybee\SecurityBundle\Entity\Page;
use Busybee\SecurityBundle\Repository\PageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class PageManager
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var array
     */
    private $pageSecurity;

    /**
     * @var Page
     */
    private $page;

    /**
     * @var Request
     */
    private $request;

    /**
     * PageManager constructor.
     * @param Session $session
     * @param ObjectManager $om
     * @param RequestStack $request
     */
    public function __construct(Session $session, ObjectManager $om, RequestStack $request)
    {
        $this->session = $session;
        $this->pageRepository = $om->getRepository(Page::class);
        $this->om = $om;
        $this->request = $request->getCurrentRequest();
    }

    /**
     * Find One by Route
     *
     * @param string $routeName
     * @param array|string $attributes
     * @return Page
     */
    public function findOneByRoute(string $routeName, $attributes = []): Page
    {
        $this->pageSecurity = $this->session->get('pageSecurity');

        if (!is_array($this->pageSecurity))
            $this->pageSecurity = [];

        $this->page = empty($this->pageSecurity[$routeName]) ? null : $this->pageSecurity[$routeName];

        if (empty($this->page)) {
            $this->page = $this->pageRepository->findOneByRoute($routeName);
            $this->pageSecurity[$routeName] = $this->page;
        }

        if (empty($this->page)) {
            $this->page = new Page();
            $this->page->setRoute($routeName);

            if (!is_array($attributes))
                $attributes = [$attributes];

            foreach ($attributes as $attribute)
                $this->page->addRole($attribute);

            $this->page->setPath($this->request->getPathInfo());
            $this->om->persist($this->page);
            $this->om->flush();
            $this->pageSecurity[$routeName] = $this->page;
        }

        if ($this->page->getCacheTime() < new \DateTime('-15 Minutes')) {
            $this->page = $this->pageRepository->findOneByRoute($routeName);
            $this->page->setCacheTime();
            $this->om->persist($this->page);
            $this->om->flush();
            $this->pageSecurity[$routeName] = $this->page;
        }

        $this->session->set('pageSecurity', $this->pageSecurity);

        return $this->page;
    }
}