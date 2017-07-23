<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\SecurityBundle\Security\VoterDetails;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Busybee\TimeTableBundle\Form\ColumnType;
use Busybee\TimeTableBundle\Form\TimeTableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class TimeTableController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $up = $this->get('timetable.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeTimeTableBundle:TimeTable:list.html.twig',
            [
                'pagination' => $up,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $entity = new TimeTable();
        if ($id > 0)
            $entity = $this->get('timetable.repository')->find($id);
        else
            $entity = new TimeTable();

        $form = $this->createForm(TimeTableType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();

            $em->persist($entity);
            $em->flush();
        }

        return $this->render('BusybeeTimeTableBundle:TimeTable:edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function editTimeTableDaysAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        if ($id == 0)
            throw new \InvalidArgumentException($this->get('translator')->trans('timetable.columns.edit.missing', [], 'BusybeeTimeTableBundle'));

        $entity = $this->get('timetable.repository')->find($id);

        $form = $this->createForm(ColumnType::class, $entity, ['tt_id' => $id]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $this->get('doctrine')->getManager();
            $om->persist($entity);
            $om->flush();
        }

        return $this->render('BusybeeTimeTableBundle:Columns:edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
                'timetable' => $entity,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function builderAction(Request $request, $id, $all = 'All')
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $tm = $this->get('timetable.manager')->setTimeTable($this->get('timetable.repository')->find($id));


        $up = $this->get('period.pagination');
        $lp = $this->get('line.pagination');
        $ap = $this->get('activity.pagination');
//        $ap->setSearch('');
//        $lp->setSearch('');


        $ap->injectRequest($request);
        $up->injectRequest($request);
        $lp->injectRequest($request);

        $up->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setDisplayResult(false);
        $gradeControl = $this->get('session')->get('gradeControl');
        $param = [];
        if (is_array($gradeControl)) {
            foreach ($gradeControl as $q => $w)
                if ($w)
                    $param[] = $q;
        }

        $search = [];
        if (!empty($param)) {
            $search['where'] = 'g.grade IN (__name__)';
            $search['parameter'] = $param;
        }

        $ap->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->addInjectedSearch($search)
            ->setDisplayResult(false);
        $lp->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $up->getDataSet();
        $lp->getDataSet();
        $ap->getDataSet();

        $report = $tm->getReport($up);

        return $this->render('BusybeeTimeTableBundle:TimeTable:builder.html.twig',
            [
                'pagination' => $up,
                'line_pagination' => $lp,
                'activity_pagination' => $ap,
                'pm' => $this->get('period.manager'),
                'all' => $all,
                'report' => $report,
                'grades' => $this->get('grade.manager')->getYearGrades(),
            ]
        );
    }

    /**
     * @param $id
     * @param $line
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addLineToPeriodAction($id, $line)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $period = $this->get('period.repository')->find($id);

        $pm = $this->get('period.manager')->injectPeriod($period);

        $pm->injectLineGroup($line);

        return $this->redirect($this->generateUrl('timetable_builder', ['id' => $period->getTimeTable()->getId(), 'all' => $id]));
    }

    /**
     * @param $id
     * @param $activity
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addActivityToPeriodAction($id, $activity)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $period = $this->get('period.repository')->find($id);

        $pm = $this->get('period.manager')->injectPeriod($period);

        $pm->injectActivityGroup($activity);

        return $this->redirect($this->generateUrl('timetable_builder', ['id' => $period->getTimeTable()->getId(), 'all' => $id]));
    }

    /**
     * @param $grade
     * @return JsonResponse
     */
    public function gradeControlAction($grade)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $session = $this->get('session');

        $gradeControl = $session->get('gradeControl');

        if (!is_array($gradeControl))
            $gradeControl = [];

        if (!isset($gradeControl[$grade]))
            $gradeControl[$grade] = true;

        $gradeControl[$grade] = $gradeControl[$grade] ? false : true;

        $session->set('gradeControl', $gradeControl);

        return new JsonResponse([], 200);
    }

    /**
     * Display TimeTable
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayAction(Request $request)
    {
        $vd = $this->get('voter.details');

        $sess = $this->get('session');

        if (!empty($request->get('closeWindow')))
            $this->get('hide.section')->HideSectionOn();
        else
            $this->get('hide.section')->HideSectionOff();

        $identifier = $sess->has('tt_identifier') ? $sess->get('tt_identifier') : $this->get('timetable.display.manager')->getTimeTableIdentifier($this->getUser());

        $vd->parseIdentifier($identifier);

        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', $vd, null);

        $tm = $this->get('timetable.display.manager');

        return $this->render('BusybeeTimeTableBundle:Display:index.html.twig',
            [
                'manager' => $tm,
            ]
        );
    }

    /**
     * Refresh Display TimeTable
     *
     * @param string $displayDate
     * @return JsonResponse
     */
    public function refreshDisplayAction($displayDate)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, null);

        $vd = $this->get('voter.details');

        $sess = $this->get('session');

        $tm = $this->get('timetable.display.manager');

        $identifier = $sess->has('tt_identifier') ? $sess->get('tt_identifier') : $tm->getTimeTableIdentifier($this->getUser());

        $vd->parseIdentifier($identifier);

        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', $vd, null);

        if ($this->getUser())
            $tm->generateTimeTable($identifier, $displayDate);

        $content = $this->renderView('BusybeeTimeTableBundle:Display:timetable.html.twig',
            [
                'manager' => $tm,
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
            ],
            200
        );
    }

    /**
     * Set TimeTable Grade
     *
     * @param $grade
     * @return JsonResponse
     */
    public function setTimeTableGradeAction($grade)
    {
        $sess = $this->get('session');

        $gc = $sess->set('tt_identifier', 'grad' . $grade);

        return new JsonResponse([], 200);
    }

    /**
     * Set TimeTable Space
     *
     * @param $space
     * @return JsonResponse
     */
    public function setTimeTableSpaceAction($space)
    {
        $sess = $this->get('session');

        $gc = $sess->set('tt_identifier', 'spac' . $space);

        return new JsonResponse([], 200);
    }

    /**
     * Set TimeTable Staff
     *
     * @param $grade
     * @return JsonResponse
     */
    public function setTimeTableStaffAction($staff)
    {
        $sess = $this->get('session');

        $gc = $sess->set('tt_identifier', 'staf' . $staff);

        return new JsonResponse([], 200);
    }
}
