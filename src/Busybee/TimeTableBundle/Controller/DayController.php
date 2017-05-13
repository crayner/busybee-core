<?php

namespace Busybee\TimeTableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DayController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    public function dayAssignAction()
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $tm = $this->get('timetable.manager');

        $year = $tm->getYear();

        if ($year->status == 'success')
            return $this->render('BusybeeTimeTableBundle:Days:assign.html.twig',
                [
                    'year' => $year,
                ]
            );

        $this->get('session')->getFlashBag()->add('warning', $this->get('translator')->trans($year->message, [], 'BusybeeTimeTableBundle'));

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
        $terms = $year->terms;

        $data = $this->renderView('@BusybeeTimeTable/Days/termTab.html.twig', [
            'termName' => $termName,
            'term' => $terms[$termName],
        ]);

        $date = new \DateTime($date);
        $formatDate = $date->format($this->get('setting.manager')->get('date.format.long'));

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
