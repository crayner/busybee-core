<?php

namespace Busybee\PersonBundle\Controller;

use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\Student;
use Busybee\PersonBundle\Form\StudentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse ;


class StudentController extends Controller
{
    public function indexAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$up = $this->get('student.pagination');
		
		$up->injectRequest($request);
		
		$up->getDataSet();

        return $this->render('BusybeePersonBundle:Student:index.html.twig',
			array(
            	'pagination' => $up,
        	)
		);
    }


    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

		$student = $this->getStudent($id);
		
		$em = $this->get('doctrine')->getManager();

		$formDefinition = $this->get('service_container')->getParameter('student');

		$editOptions = array();

        $form = $this->createForm(StudentType::class, $student);

		foreach($formDefinition as $extra)
		{
            if (isset($extra['form']) && isset($extra['name']))
			{
				$options = array();
				if (! empty($extra['options']) && is_array($extra['options']))
					$options = $extra['options'];
				$name = $extra['data']['name'];
                $data = $this->get($name)->findOneByStudent($student->getId());
                $data->setStudent($student);
				$options['data'] = $data ;
				$form->add($extra['name'], $extra['form'], $options);
				$name = $extra['name'];
				$student->$name = $options['data'];
				
			}
			if (isset($extra['script']))
				$editOptions['script'][] = $extra['script'];
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
            $validator = $this->get('validator');

            $ok = true ;
            foreach($formDefinition as $defined)
			{
				$req = isset($defined['request']['post']) ? $defined['request']['post'] : null ;
				if (! is_null($req) && isset($student->$req))
				{
                    $entity = $student->$req;
                    $errors = $validator->validate($entity);
                    if (count($errors) > 0)
                    {
                        foreach($errors as $w){
                            $subForm = $form->get($req);
                            $field = $w->getConstraint()->errorPath;
                            if (null !== $subForm->get($field))
                                $subForm->get($field)->addError(new FormError($w->getMessage(), $w->getParameters()));
                        }
                        $ok = false ;
                    }
					if ($ok) {
                        $em->persist($student->$req);
                    }
				}
			}
			if ($ok) {
                $em->persist($student);
                $em->flush();
                $id = $student->getId();

                return new RedirectResponse($this->generateUrl('student_edit', array('id' => $id)));
            }
		} 

		$editOptions['id'] = $id;
		$editOptions['form'] = $form->createView();
		$editOptions['fullForm'] = $form;

        return $this->render('BusybeePersonBundle:Student:edit.html.twig',
			$editOptions	
		);
    }


    private function getStudent($id)
    {
		$student = new Student();
		if ($id !== 'Add')
			$student = $this->get('student.repository')->find($id);
		$student->cancelURL = $this->generateUrl('student_edit', array('id' => $id));

		return $student ;
	}

    public function toggleAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

        $person = $this->get('person.repository')->find($id);

        $student = $this->get('student.repository')->findOneByPerson($id);

        if (!$person instanceof Person)
            return new JsonResponse(
                array(
                    'message' => '<div class="alert alert-danger fadeAlert">'.$this->get('translator')->trans('student.toggle.personMissing', array(), 'BusybeePersonBundle').'</div>',
                    'status' => 'failed'
                ),
                200
            );
        $em = $this->get('doctrine')->getManager();
        if ($student instanceof Student) {
            if ($this->get('person.manager')->canDeleteStudent($person, $this->getParameter('student'))) {
                $this->get('person.manager')->deleteStudent($person, $this->getParameter('student'));
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('student.toggle.removeSuccess', array('%name%' => $student->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'removed',
                    ),
                    200
                );
            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('student.toggle.removeRestricted', array('%name%' => $student->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'falied',
                    ),
                    200
                );
            }
        } else {
            if ($this->get('person.manager')->canBeStudent($person)) {
                $student = new Student();
                $student->setPerson($person);
                $person->setStudent($student);
                $em->persist($person);
                $em->flush();
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('student.toggle.addSuccess', array('%name%' => $student->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'added',
                    ),
                    200
                );
            } else {
                return new JsonResponse(
                    array(
                        'message' => '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('student.toggle.addRestricted', array('%name%' => $person->getFormatName()), 'BusybeePersonBundle') . '</div>',
                        'status' => 'failed',
                    ),
                    200
                );

            }
        }
    }

    private function formatAddress($address)
    {
		return $this->get('address.manager')->formatAddress($address);
	}
}
