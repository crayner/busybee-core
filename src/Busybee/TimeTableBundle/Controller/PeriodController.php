<?php

namespace Busybee\TimeTableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $column = $this->get('column.repository')->createQueryBuilder('c')
            ->leftJoin('c.periods', 'p')
            ->where('p.id = :period_id')
            ->setParameter('period_id', $id)
            ->getQuery()
            ->getSingleResult();

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
}