<?php

namespace Busybee\InstituteBundle\Controller ;

use Busybee\InstituteBundle\Entity\CampusResource;
use Busybee\InstituteBundle\Form\CampusResourceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\InstituteBundle\Form\CampusType ;
use Busybee\InstituteBundle\Entity\Campus ;

class CampusController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

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
        dump($form);
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
    public function resourceAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $up = $this->get('campusResource.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeInstituteBundle:Campus:resources.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }
    public function editResourceAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $campus = new CampusResource();

        $id = $request->get('id');
        if (intval($id) > 0)
            $campus = $this->get('campusResource.repository')->find($id);

        if (! is_null($campus->getCampus())) $campus->getCampus()->getName();
        if (! is_null($campus->getStaff1())) $campus->getStaff1()->getPerson();
        if (! is_null($campus->getStaff2())) $campus->getStaff2()->getPerson();

        $campus->cancelURL = $this->get('router')->generate('campus_resource_manage');

        $form = $this->createForm(CampusResourceType::class, $campus);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->get('doctrine')->getManager();

            if (is_null($campus->getStaff1()) && ! is_null($campus->getStaff2()))
            {
                $campus->setStaff1($campus->getStaff2());
                $campus->setStaff2(null);
            }
            if ($campus->getStaff1() == $campus->getStaff2() && ! is_null($campus->getStaff1()))
                $campus->setStaff2(null);

            $em->persist($campus);
            $em->flush();

			return new RedirectResponse($this->get('router')->generate('campus_resource_edit', array('id' => $campus->getId())));
        }

        return $this->render('BusybeeInstituteBundle:Campus:resourceEdit.html.twig', array('id' => $id, 'form' => $form->createView()));
    }
}
