<?php

namespace Busybee\InstituteBundle\Controller ;

use Busybee\InstituteBundle\Entity\StudentYear;
use Busybee\InstituteBundle\Form\StudentYearType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request ;


class YearController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $year = new StudentYear();
        if (intval($id) > 0)
            $year = $this->get('studentYear.repository')->find($id);

        $year->cancelURL = $this->get('router')->generate('groups_years');

        $form = $this->createForm(StudentYearType::class, $year);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->get('doctrine')->getManager();
            $em->persist($year);
            $em->flush();

            return new RedirectResponse($this->get('router')->generate('student_year_edit', array('id' => $year->getId())));
        }

        return $this->render('BusybeeInstituteBundle:Year:index.html.twig', array('form' => $form->createView()));
    }
}
