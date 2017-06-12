<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\TimeTableBundle\Entity\TimeTable;
use Busybee\TimeTableBundle\Form\ColumnType;
use Busybee\TimeTableBundle\Form\TimeTableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function builderAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $this->get('timetable.manager')->setTimeTable($this->get('timetable.repository')->find($id));

        $up = $this->get('period.pagination');
        $lp = $this->get('line.pagination');
        $ap = $this->get('activity.pagination');
        $ap->setSearch('');
        $lp->setSearch('');

        $ap->injectRequest($request);
        $up->injectRequest($request);
        $lp->injectRequest($request);

        $up->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setDisplayResult(false);
        $ap->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setDisplayResult(false);
        $lp->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->setDisplayResult(false);

        $up->getDataSet();
        $lp->getDataSet();
        $ap->getDataSet();

        return $this->render('BusybeeTimeTableBundle:TimeTable:builder.html.twig',
            [
                'pagination' => $up,
                'line_pagination' => $lp,
                'activity_pagination' => $ap,
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

        return $this->redirect($this->generateUrl('timetable_builder', ['id' => $period->getTimeTable()->getId()]));
    }
}
