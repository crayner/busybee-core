<?php

namespace Busybee\Facility\InstituteBundle\Controller;

use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\Facility\InstituteBundle\Form\DepartmentType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DepartmentController extends BusybeeController
{


	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

		$entity = new Department();
		$id     = $request->get('id');
		if (intval($id) > 0)
			$entity = $this->get('department.repository')->find($id);

		$form = $this->createForm(DepartmentType::class, $entity);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($entity);
			$em->flush();
			if ($id == 'Add')
			{
				foreach ($entity->getStaff()->toArray() as $deptStaff)
				{
					$deptStaff->setDepartment($entity);
					$em->persist($deptStaff);
					$em->flush();
				}

				return new RedirectResponse($this->generateUrl('department_edit', ['id' => $entity->getId()]));
			}

			foreach ($entity->getStaff()->toArray() as $deptStaff)
			{
				if (empty($deptStaff->getDepartment()))
				{
					$deptStaff->setDepartment($entity);
					$em->persist($deptStaff);
					$em->flush();
				}
			}

		}

		return $this->render('BusybeeInstituteBundle:Department:edit.html.twig', array(
				'form'     => $form->createView(),
				'fullForm' => $form,
			)
		);
	}
}
