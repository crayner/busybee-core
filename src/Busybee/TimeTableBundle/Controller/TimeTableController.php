<?php

namespace Busybee\TimeTableBundle\Controller;

use Busybee\TimeTableBundle\Entity\TimeTable;
use Busybee\TimeTableBundle\Form\ColumnType;
use Busybee\TimeTableBundle\Form\TimeTableDaysType;
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
    public function editAction(Request $request, $id, $currentSearch = null)
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
                'currentSearch' => $currentSearch,
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function editTimeTableDaysAction(Request $request, $id, $currentSearch = null)
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
                'currentSearch' => $currentSearch,
                'fullForm' => $form,
                'timetable' => $entity,
            ]
        );
    }
}
