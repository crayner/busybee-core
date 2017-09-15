<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\TimeTableBundle\Entity\Line;
use Busybee\TimeTableBundle\Form\LineType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class LineController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $up = $this->get('line.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeTimeTableBundle:Line:list.html.twig',
            [
                'pagination' => $up,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function manageAction(Request $request, $id, $closeWindow)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $entity = new line();
        if ($id > 0)
            $entity = $this->get('line.repository')->find($id);

        $year = $this->get('current.year.currentYear');

        $entity->setYear($year);

        $form = $this->createForm(lineType::class, $entity, ['year_data' => $year]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $em->persist($entity);
            $em->flush();

            if ($id == 'Add') {
                $close = [];
                if (!empty($closeWindow))
                    $close = ['closeWindow' => '_closeWindow'];

                return new RedirectResponse($this->generateUrl('line_manage', array_merge(['id' => $entity->getId()], $close)));
            }
        }

        return $this->render('BusybeeTimeTableBundle:Line:manage.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
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

        $lgm = $this->get('line.manager');

        $year = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());

        $lgm->generateReport($id, $year);

        $data = $lgm->getReport();

        return $this->render('BusybeeTimeTableBundle:Line:report.html.twig',
            [
                'report' => $data['report'],
            ]
        );

    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $lm = $this->get('line.manager');

        $lm->deleteLine($id);

        return new RedirectResponse($this->generateUrl('line_list'));
    }

    /**
     * @param $tt
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchPeriodsAction($tt, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $lgm = $this->get('line.manager');

        $data = $lgm->searchForSuitablePeriods($tt, $id);

        return $this->render('BusybeeTimeTableBundle:Line:search.html.twig',
            [
                'report' => $data,
                'manager' => $lgm,
            ]
        );
    }
}