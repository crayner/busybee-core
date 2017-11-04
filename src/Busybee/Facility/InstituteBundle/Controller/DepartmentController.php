<?php
namespace Busybee\Facility\InstituteBundle\Controller;

use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\Facility\InstituteBundle\Entity\DepartmentStaff;
use Busybee\Facility\InstituteBundle\Form\DepartmentType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
			$entity = $this->get('busybee_facility_institute.repository.department_repository')->find($id);

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

				return $this->redirectToRoute('department_edit', ['id' => $entity->getId()]);
			}

		}

		return $this->render('BusybeeInstituteBundle:Department:edit.html.twig', array(
				'form'     => $form->createView(),
				'fullForm' => $form,
			)
		);
	}

	public function removeStaffAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

		$om = $this->getDoctrine()->getManager();

		$ds = $om->getRepository(DepartmentStaff::class)->find($id);

		if ($ds instanceof DepartmentStaff)
		{

			$data            = [];
			$data['status']  = 'success';
			$data['message'] = $this->get('translator')->trans('department.staff.remove.success', [], 'BusybeeInstituteBundle');
			try
			{
				$om->remove($ds);
				$om->flush();
			}
			catch (\Exception $e)
			{
				$data['status']  = 'error';
				$data['message'] = $this->get('translator')->trans('department.staff.remove.failure', [], 'BusybeeInstituteBundle');
			}

			return new JsonResponse($data, 200);
		}

		$data            = [];
		$data['message'] = $this->get('translator')->trans('department.staff.remove.missing', [], 'BusybeeInstituteBundle');
		$data['status']  = 'warning';

		return new JsonResponse($data, 200);

	}
}
