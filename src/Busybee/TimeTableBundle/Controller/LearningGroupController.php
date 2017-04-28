<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\TimeTableBundle\Entity\LearningGroups;
use Busybee\TimeTableBundle\Form\LearningGroupsType;
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

        $up = $this->get('learning.groups.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeTimeTableBundle:LearningGroups:list.html.twig',
            [
                'pagination' => $up,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function manageAction(Request $request, $id, $currentSearch = null)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $entity = new LearningGroups();
        if ($id > 0)
            $entity = $this->get('learning.groups.repository')->find($id);

        $form = $this->createForm(LearningGroupsType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $em->persist($entity);
            $em->flush();
        }

        return $this->render('BusybeeTimeTableBundle:LearningGroups:manage.html.twig',
            [
                'form' => $form->createView(),
                'currentSearch' => $currentSearch,
            ]
        );
    }
}
