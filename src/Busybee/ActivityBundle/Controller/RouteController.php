<?php

namespace Busybee\ActivityBundle\Controller;

use Busybee\Core\HomeBundle\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RouteController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

    public function indexAction($id)
    {
        $vd = $this->get('busybee_core_security.security.voter_details');

        $vd->userIdentifier($this->get('person.manager'), $this->getUser())
            ->activityIdentifier($id);

        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', $vd, null);

        $target = $vd->getIdentifierType();

        if (!is_null($target) && method_exists($this, $target))
            return $this->$target($vd->getActivity()->getActivity());

        throw new Exception($this->get('translator.default')->trans('activity.display.type.exception', [], 'BusybeeActivityBundle'));
    }

    private function staf($activity)
    {
        return $this->render('@BusybeeActivity/Display/staff.html.twig',
            [
                'activity' => $activity,
            ]
        );
    }
}
