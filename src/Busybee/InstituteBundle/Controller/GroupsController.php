<?php

namespace Busybee\InstituteBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;


class GroupsController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function yearsAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $up = $this->get('studentYear.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

		return $this->render('BusybeeInstituteBundle:Groups:years.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }
}
