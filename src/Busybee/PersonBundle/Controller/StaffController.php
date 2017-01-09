<?php

namespace Busybee\PersonBundle\Controller;

use Busybee\PersonBundle\Entity\Staff;
use Busybee\PersonBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class StaffController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    /**
     * @param $id
     * @return JsonResponse
     */
    public function toggleAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $person = $this->get('person.repository')->find($id);

        $staff = $this->get('staff.repository')->findOneByPerson($id);

        if (!$person instanceof Person)
            return new JsonResponse(
                array(
                    'message' => '<div class="alert alert-danger fadeAlert">'.$this->get('translator')->trans('staff.toggle.personMissing', array(), 'BusybeePersonBundle').'</div>',
                    'status' => 'failed'
                ),
                200
            );
        $em = $this->get('doctrine')->getManager();
        if ($staff instanceof Staff)
        {
            if ($this->get('person.manager')->canDeleteStaff($person)) {
                $this->get('person.manager')->deleteStaff($person);
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('staff.toggle.removeSuccess', array('%name%' => $staff->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'removed',
                    ),
                    200
                );
            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('staff.toggle.removeRestricted', array('%name%' => $staff->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        } else {
            if ($this->get('person.manager')->canBeStaff($person)) {
                $staff = new Staff();
                $staff->setPerson($person);
                $staff->setType('');
                $staff->setJobTitle('');
                $em->persist($staff);
                $em->flush();
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('staff.toggle.addSuccess', array('%name%' => $staff->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'added',
                    ),
                    200
                );
            } else{
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('staff.toggle.addRestricted', array('%name%' => $person->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        }
    }
}
