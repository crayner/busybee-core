<?php

namespace Busybee\ActivityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RouteController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    public function indexAction($id)
    {
        $vd = $this->get('voter.details');

        $vd->userIdentifier($this->get('person.manager'), $this->getUser())
            ->activityIdentifier($id);

        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', $vd, null);

        return $this->render('BusybeeActivityBundle:Default:index.html.twig',
            [
                'id' => $id,
                'vd' => $vd,
            ]
        );
    }
}
