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
        if ($currentSearch == 'null')
            $currentSearch = null;


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
}