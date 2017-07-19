<?php

namespace Busybee\PersonBundle\Controller;

use Busybee\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends Controller
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

        $user = $this->get('user.repository')->findOneByPerson($id);

        if (!$person instanceof Person)
            return new JsonResponse(
                array(
                    'message' => '<div class="alert alert-danger fadeAlert">'.$this->get('translator')->trans('user.toggle.personMissing', array(), 'BusybeePersonBundle').'</div>',
                    'status' => 'failed'
                ),
                200
            );
        $em = $this->get('doctrine')->getManager();
        if ($user instanceof User)
        {
            if ($this->get('person.manager')->canDeleteUser($person)) {
                $this->get('person.manager')->deleteUser($person);
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('user.toggle.removeSuccess', array('%name%' => $user->formatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'removed',
                    ),
                    200
                );
            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('user.toggle.removeRestricted', array('%name%' => $user->formatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        } else {
            if ($this->get('person.manager')->canBeUser($person)) {
                $user = new User();
                $user->setPerson($person);
                $user->setEmail($person->getEmail());
                $user->setEmailCanonical($person->getEmail());
                $user->setUsername($person->getEmail());
                $user->setUsernameCanonical($person->getEmail());
                $user->setLocale($this->getParameter('locale'));
                $user->setEnabled(true);
                $user->setLocked(false);
                $user->setExpired(false);
                $user->setCredentialsExpired(true);
                $user->setPassword(password_hash(uniqid(), PASSWORD_BCRYPT));
                $person->setUser($user);
                $em->persist($person);
                $em->flush();
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('user.toggle.addSuccess', array('%name%' => $user->formatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'added',
                    ),
                    200
                );
            } else{
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('user.toggle.addRestricted', array('%name%' => $person->formatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        }
    }
}
