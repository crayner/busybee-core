<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DayController extends BusybeeController
{


    public function dayAssignAction()
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $year = $this->get('timetable.manager')->getTTYear();

        if ($year->status == 'success')
            return $this->render('BusybeeTimeTableBundle:Days:assign.html.twig',
                [
                    'year' => $year,
                ]
            );

        $this->get('session')->getFlashBag()->add('warning', $this->get('translator')->trans($year->message, empty($year->options) ? [] : $year->options, 'BusybeeTimeTableBundle'));

        return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $year->tt->getId()]));
    }

    /**
     * @param $date
     * @return JsonResponse
     */
    public function rotateToggleAction($date, $termName)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $tm = $this->get('timetable.manager');
        $termName = str_replace('_', ' ', $termName);

        if (!$tm->testDate($date)) {
            return new JsonResponse(
                [
                    'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('timetable.rotate.toggle.failed', array(), 'BusybeeTimeTableBundle') . '</div>',
                    'status' => 'failed'
                ],
                200
            );
        }

        $removed = $tm->toggleRotateStart($date);

        $year = $tm->getYear();
        $terms = $year->getTerms();

        $data = $this->renderView('@BusybeeTimeTable/Days/termTab.html.twig', [
            'termName' => $termName,
            'term' => $terms[$termName],
        ]);

        $date       = new \DateTime($date);
	    $formatDate = $date->format($this->get('busybee_core_system.setting.setting_manager')->get('date.format.long'));

        return new JsonResponse(
            [
                'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('timetable.rotate.toggle.' . $removed, ['%date%' => $formatDate], 'BusybeeTimeTableBundle') . '</div>',
                'data' => $data,
                'status' => 'success',
            ],
            200
        );
    }
}
