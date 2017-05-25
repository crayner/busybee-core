<?php
namespace Busybee\StudentBundle\Controller;

use Busybee\PersonBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param $id
     * @return JsonResponse
     */
    public function toggleAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

        $person = $this->get('person.repository')->find($id);

        if (!$person instanceof Person)
            return new JsonResponse(
                array(
                    'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('student.toggle.personMissing', array(), 'BusybeeStudentBundle') . '</div>',
                    'status' => 'failed'
                ),
                200
            );

        $em = $this->get('doctrine')->getManager();

        if (!$person->getStudentQuestion()) {
            if ($this->get('person.manager')->canBeStudent($person)) {
                $this->get('person.manager')->createStudent($person);
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('student.toggle.addSuccess', array('%name%' => $person->getFormatName()), 'BusybeeStudentBundle') . '</div>',
                        'status' => 'added',
                    ),
                    200
                );
            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('student.toggle.addRestricted', array('%name%' => $person->getFormatName()), 'BusybeeStudentBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        } elseif ($person->getStudentQuestion()) {
            if ($this->get('person.manager')->canDeleteStudent($person, $this->getParameter('person'))) {
                $this->get('person.manager')->deleteStudent($person, $this->getParameter('person'));
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('student.toggle.removeSuccess', array('%name%' => $person->getFormatName()), 'BusybeeStudentBundle') . '</div>',
                        'status' => 'removed',
                    ),
                    200
                );

            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('student.toggle.removeRestricted', array('%name%' => $person->getFormatName()), 'BusybeeStudentBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );
            }
        }
    }

    /**
     * @param Request $request
     * @param null $limit
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $up = $this->get('student.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeStaffBundle:Staff:index.html.twig',
            array(
                'pagination' => $up,
            )
        );
    }

}