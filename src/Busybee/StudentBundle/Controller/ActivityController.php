<?php

namespace Busybee\StudentBundle\Controller;

use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Form\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param Request $request
     * @param null $currentSearch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, $currentSearch = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $up = $this->get('activity.pagination');

        $up->injectRequest($request, $currentSearch);

        $up->getDataSet();

        return $this->render('BusybeeStudentBundle:Activity:list.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $currentSearch)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->get('doctrine')->getManager();

        $entity = intval($id) > 0 ? $this->get('activity.repository')->find($id) : new Activity();

        $editOptions = array();

        $form = $this->createForm(ActivityType::class, $entity);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $id = $entity->getId();

            return new RedirectResponse($this->generateUrl('student_activity_edit', array('id' => $id, 'currentSearch' => $currentSearch)));
        }

        $editOptions['id'] = $id;
        $editOptions['form'] = $form->createView();
        $editOptions['fullForm'] = $form;
        $editOptions['currentSearch'] = $currentSearch;

        return $this->render('BusybeeStudentBundle:Activity:edit.html.twig',
            $editOptions
        );
    }
}