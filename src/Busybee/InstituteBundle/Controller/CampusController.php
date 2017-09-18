<?php

namespace Busybee\InstituteBundle\Controller ;

use Busybee\InstituteBundle\Entity\Space;
use Busybee\InstituteBundle\Form\SpaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\InstituteBundle\Form\CampusType ;
use Busybee\InstituteBundle\Entity\Campus ;

class CampusController extends BusybeeController
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $campus = new Campus();
        $id = $request->get('id');
        if (intval($id) > 0)
            $campus = $this->get('campus.repository')->find($id);


        $form = $this->createForm(CampusType::class, $campus);
        if (intval($id) > 0)
            $form->get('locationList')->setData($id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->get('doctrine')->getManager();
            $em->persist($campus);
            $em->flush();

        }

        return $this->render('BusybeeInstituteBundle:Campus:index.html.twig', array(
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

        $space = new Space();

        $id = $request->get('id');
        if (intval($id) > 0)
            $space = $this->get('space.repository')->find($id);

        $space->cancelURL = $this->get('router')->generate('campus_space_manage');

        $form = $this->createForm(SpaceType::class, $space);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->get('doctrine')->getManager();

            $em->persist($space);
            $em->flush();


            if ($id === 'Add')
                return new RedirectResponse($this->get('router')->generate('campus_space_edit', array('id' => $space->getId())));
        }

        return $this->render('BusybeeInstituteBundle:Campus:spaceEdit.html.twig', array('id' => $id, 'form' => $form->createView()));
    }

    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function duplicateSpaceAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $id = $request->get('space')['duplicateid'];

        if ($id === "Add")
            $space = new Space();
        else
            $space = $this->get('space.repository')->find($id);

        $space->cancelURL = $this->generateUrl('campus_space_manage');

        $form = $this->createForm(SpaceType::class, $space);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $em->persist($space);
            $em->flush();

            $route = $this->generateUrl('campus_space_edit', ['id' => 'Add']);
            $space->setId(null);
            $space->setName(null);
            $form = $this->createForm(SpaceType::class, $space, ['action' => $route]);
            $id = 'Add';
        }


        return $this->render('BusybeeInstituteBundle:Campus:spaceEdit.html.twig',
            [
                'id' => $id,
                'form' => $form->createView(),
            ]
        );
    }
}
