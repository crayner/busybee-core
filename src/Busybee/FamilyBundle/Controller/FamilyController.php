<?php

namespace Busybee\FamilyBundle\Controller;

use Busybee\FamilyBundle\Form\FamilyType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FamilyController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param Request $request
     * @param null $currentSearch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $currentSearch = null)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $up = $this->get('family.pagination');

        $up->injectRequest($request, $currentSearch);

        $up->getDataSet();

        return $this->render('BusybeeFamilyBundle:Family:index.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @param null $currentSearch
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $currentSearch = null)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $family = $this->get('family.repository')->find($id);

        $form = $this->createForm(FamilyType::class, $family);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->get('doctrine')->getManager();
            $em->persist($family);
            $em->flush();
            $id = $family->getId();

            return new RedirectResponse($this->generateUrl('family_edit', array('id' => $id, 'currentSearch' => $currentSearch)));

        }

        $editOptions = array();
        $editOptions['id'] = $id;
        $editOptions['form'] = $form->createView();
        $editOptions['fullForm'] = $form;
        $editOptions['currentSearch'] = $currentSearch;

        return $this->render('BusybeeFamilyBundle:Family:edit.html.twig',
            $editOptions
        );
    }
}