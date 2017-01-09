<?php

namespace Busybee\PersonBundle\Controller;

use Busybee\PersonBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class PeopleController extends Controller
{
    public function toggleAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

        $person = $this->get('person.repository')->find($id);

        $careGiver = $this->get('caregiver.repository')->findOneByPerson($id);

        if (!$person instanceof Person)
            return new JsonResponse(
                array(
                    'message' => '<div class="alert alert-danger fadeAlert">'.$this->get('translator')->trans('caregiver.toggle.personMissing', array(), 'BusybeePersonBundle').'</div>',
                    'status' => 'failed'
                ),
                200
            );
        $em = $this->get('doctrine')->getManager();
        if ($careGiver instanceof CareGiver)
        {
            if ($this->get('person.manager')->canDeleteCareGiver($person)) {
                $this->get('person.manager')->deleteCareGiver($person);
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('caregiver.toggle.removeSuccess', array('%name%' => $careGiver->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'removed',
                    ),
                    200
                );
            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('caregiver.toggle.removeRestricted', array('%name%' => $careGiver->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        } else {
            if ($this->get('person.manager')->canBeCareGiver($person)) {
                $careGiver = new CareGiver();
                $careGiver->setPerson($person);
                $em->persist($careGiver);
                $em->flush();
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('caregiver.toggle.addSuccess', array('%name%' => $careGiver->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'added',
                    ),
                    200
                );
            } else{
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('caregiver.toggle.addRestricted', array('%name%' => $person->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        }
    }
}
