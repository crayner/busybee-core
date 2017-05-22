<?php

namespace Busybee\TimeTableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ColumnController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param $id
     * @param $page
     * @param $currentSearch
     * @return RedirectResponse
     */
    public function removeAction($id, $currentSearch)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $column = $this->get('column.repository')->find($id);
        $currentSearch = $currentSearch === 'null' ? null : $currentSearch;


        if (empty($column)) {
            $this->get('session')->getFlashBag()->add('success', 'column.remove.missing');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $column->getTimeTable()->getId()]));
        }


        if (!$column->canDelete($id)) {
            $this->get('session')->getFlashBag()->add('warning', 'column.remove.locked');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $column->getTimeTable()->getId()]));
        }

        try {
            $om = $this->get('doctrine')->getManager();
            $om->remove($column);
            $om->flush();
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('danger', 'column.remove.error');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $column->getTimeTable()->getId()]));
        }

        $this->get('session')->getFlashBag()->add('success', 'column.remove.success');

        return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $column->getTimeTable()->getId()]));
    }

    /**
     * @param $id
     * @param $currentSearch
     * @return RedirectResponse
     */
    public function resetTimesAction($id, $currentSearch)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $currentSearch = $currentSearch === 'null' ? null : $currentSearch;

        $tt = $this->get('timetable.repository')->find($id);
        if (empty($tt)) {
            $this->get('session')->getFlashBag()->add('warning', 'column.resettime.missing');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $id]));
        }

        $sm = $this->get('setting.manager');
        $begin = new \DateTime('1970-01-01 ' . $sm->get('SchoolDay.Begin'));
        $finish = new \DateTime('1970-01-01 ' . $sm->get('SchoolDay.Finish'));
        $om = $this->get('doctrine')->getManager();

        if ($tt->getColumns()->count() > 0) {
            try {
                foreach ($tt->getColumns() as $column) {
                    $column->setStart($begin);
                    $column->setEnd($finish);
                    $om->persist($column);
                }
                $om->flush();
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('danger', 'column.resettime.error');
                return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $column->getTimeTable()->getId()]));
            }
        }

        $this->get('session')->getFlashBag()->add('success', 'column.resettime.success');
        return new RedirectResponse($this->generateUrl('timetable_edit', ['currentSearch' => $currentSearch, 'id' => $column->getTimeTable()->getId()]));
    }
}