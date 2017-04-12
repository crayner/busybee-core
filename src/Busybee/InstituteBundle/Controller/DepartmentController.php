<?php

namespace Busybee\InstituteBundle\Controller;

use Busybee\InstituteBundle\Entity\Department;
use Busybee\InstituteBundle\Form\DepartmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DepartmentController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $entity = new Department();
        $id = $request->get('id');
        if (intval($id) > 0)
            $entity = $this->get('department.repository')->find($id);


        $form = $this->createForm(DepartmentType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush();

        }

        return $this->render('BusybeeInstituteBundle:Department:edit.html.twig', array(
                'form' => $form->createView(),
                'fullForm' => $form,
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function spaceAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $up = $this->get('space.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeInstituteBundle:Campus:spaces.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editSpaceAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $campus = new Space();

        $id = $request->get('id');
        if (intval($id) > 0)
            $campus = $this->get('space.repository')->find($id);

        if (!is_null($campus->getCampus())) $campus->getCampus()->getName();
        if (!is_null($campus->getStaff1())) $campus->getStaff1()->getPerson();
        if (!is_null($campus->getStaff2())) $campus->getStaff2()->getPerson();

        $campus->cancelURL = $this->get('router')->generate('campus_space_manage');

        $form = $this->createForm(SpaceType::class, $campus);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            if (is_null($campus->getStaff1()) && !is_null($campus->getStaff2())) {
                $campus->setStaff1($campus->getStaff2());
                $campus->setStaff2(null);
            }
            if ($campus->getStaff1() == $campus->getStaff2() && !is_null($campus->getStaff1()))
                $campus->setStaff2(null);

            $em->persist($campus);
            $em->flush();

            return new RedirectResponse($this->get('router')->generate('campus_space_edit', array('id' => $campus->getId())));
        }

        return $this->render('BusybeeInstituteBundle:Campus:spaceEdit.html.twig', array('id' => $id, 'form' => $form->createView()));
    }
}
