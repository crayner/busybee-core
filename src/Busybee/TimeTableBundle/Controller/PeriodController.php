<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\TimeTableBundle\Form\PeriodActivityType;
use Busybee\TimeTableBundle\Form\PeriodPlanType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PeriodController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param $id
     * @param $page
     * @return JsonResponse
     */
    public function removeAction($id, $page)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $period = $this->get('period.repository')->find($id);
        $status = 'success';


        if (empty($period)) {
            return new JsonResponse(
                array(
                    'status' => 'error',
                    'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('period.remove.missing', [], 'BusybeeTimeTableBundle') . '</div>',
                ),
                200
            );
        }

        $message = '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('period.remove.locked', [], 'BusybeeTimeTableBundle') . '</div>';

        if (!$this->get('period.manager')->canDelete($id))
            return new JsonResponse(
                array(
                    'status' => 'warning',
                    'message' => $message,
                ),
                200
            );

        $om = $this->get('doctrine')->getManager();
        $om->remove($period);
        $om->flush();


        $message = '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('period.remove.success', [], 'BusybeeTimeTableBundle') . '</div>';

        return new JsonResponse(
            array(
                'page' => $page,
                'status' => $status,
                'message' => $message,
            ),
            200
        );
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activityPlanAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $period = $this->get('period.repository')->find($id);

        $pm = $this->get('period.manager')->injectPeriod($period->getId());

        $form = $this->createForm(PeriodActivityType::class, $period, ['year_data' => $this->get('busybee_security.user_manager')->getSystemYear($this->getUser()), 'manager' => $pm]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $this->get('doctrine')->getManager();
            $om->persist($period);
            $om->flush();
        }

        return $this->render('BusybeeTimeTableBundle:Plan:index.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
                'period' => $pm,
            ]
        );

    }
}