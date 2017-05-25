<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\TimeTableBundle\Entity\ActivityGroups;
use Busybee\TimeTableBundle\Form\ActivityGroupsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class ActivityGroupController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $up = $this->get('activity.groups.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeTimeTableBundle:ActivityGroups:list.html.twig',
            [
                'pagination' => $up,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function manageAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $entity = new ActivityGroups();
        if ($id > 0)
            $entity = $this->get('activity.groups.repository')->find($id);

        $year = $this->get('busybee_security.user_manager')->getSystemYear($this->getUser());

        $form = $this->createForm(ActivityGroupsType::class, $entity, ['year_data' => $year]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $em->persist($entity);
            $em->flush();

            if ($id == 'Add')
                return new RedirectResponse($this->generateUrl('activity_group_manage', ['id' => $entity->getId()]));
        }

        return $this->render('BusybeeTimeTableBundle:ActivityGroups:manage.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param integer $id
     * @return  String
     */
    public function testAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $lgm = $this->get('activity.groups.manager');

        $year = $this->get('busybee_security.user_manager')->getSystemYear($this->getUser());

        $lgm->generateReport($id, $year);

        $data = $lgm->getReport();

        return $this->render('BusybeeTimeTableBundle:ActivityGroups:report.html.twig',
            [
                'report' => $data['report'],
            ]
        );

    }
}