<?php

namespace Busybee\StudentBundle\Controller;

use Busybee\StudentBundle\Entity\Activity;
use Busybee\StudentBundle\Entity\Student;
use Busybee\StudentBundle\Form\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $up = $this->get('activity.pagination');

        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('BusybeeStudentBundle:Activity:list.html.twig',
            array(
                'pagination' => $up,
                'am' => $this->get('activity.manager'),
            )
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $closeWindow)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->get('doctrine')->getManager();

        $entity = intval($id) > 0 ? $this->get('activity.repository')->find($id) : new Activity();

        $entity->setYear($this->get('current.year.currentYear'));

        $editOptions = array();

        $form = $this->createForm(ActivityType::class, $entity, ['year_data' => $this->get('current.year.currentYear')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();

            if ($id === 'Add') {
                $close = [];
                if (!empty($closeWindow))
                    $close = ['closeWindow' => '_closeWindow'];

                $id = $entity->getId();
                return new RedirectResponse($this->generateUrl('student_activity_edit', array_merge(['id' => $id], $close)));
            }
        }

        $editOptions['id'] = $id;
        $editOptions['form'] = $form->createView();
        $editOptions['fullForm'] = $form;
        $editOPtions['manager'] = $this->get('student.manager');

        return $this->render('BusybeeStudentBundle:Activity:edit.html.twig',
            $editOptions
        );
    }

    public function studentListAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $activity = $this->get('activity.repository')->find($id);

        $data = empty($activity) || empty($activity->getStudents()) ? [] : $activity->getStudents()->toArray();

        $students = [];
        foreach ($data as $student)
            $students[] = $student->getId();

        return new JsonResponse(
            array(
                'students' => json_encode($students),
                'status' => 'success',
            ),
            200
        );
    }

    /**
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->get('activity.manager')->deleteActivity($id);

        return new RedirectResponse($this->generateUrl('student_activities'));
    }
}