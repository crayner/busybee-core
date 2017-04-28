<?php

namespace Busybee\TimeTableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class LearningGroupController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, $currentSearch = null)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        return $this->render('@BusybeeTimeTable/LearningGroups/list.html.twig');
    }
}
