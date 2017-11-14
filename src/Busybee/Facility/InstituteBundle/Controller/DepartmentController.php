<?php
namespace Busybee\Facility\InstituteBundle\Controller;

use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\Facility\InstituteBundle\Entity\DepartmentMember;
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

		if (intval($id) > 0)
			$entity = $this->getDoctrine()->getRepository(Department::class)->find($id);

		$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

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

			$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

		}

		return $this->render('BusybeeInstituteBundle:Department:edit.html.twig', array(
				'form'     => $form->createView(),
				'fullForm' => $form,
			)
		);
	}

	/**
	 * @param $id
	 *
	 * @return JsonResponse
	 */
	public function removeMemberAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

		$om = $this->getDoctrine()->getManager();

		$ds = $om->getRepository(DepartmentMember::class)->find($id);

		if ($ds instanceof DepartmentMember)
		{

			$data            = [];
			$data['status']  = 'success';
			$data['message'] = $this->get('translator')->trans('department.member.remove.success', [], 'BusybeeInstituteBundle');
			try
			{
				$om->remove($ds);
				$om->flush();
			}
			catch (\Exception $e)
			{
				$data['status']  = 'error';
				$data['message'] = $this->get('translator')->trans('department.member.remove.failure', [], 'BusybeeInstituteBundle');
			}

			return new JsonResponse($data, 200);
		}

		$data            = [];
		$data['message'] = $this->get('translator')->trans('department.member.remove.missing', [], 'BusybeeInstituteBundle');
		$data['status']  = 'warning';

		return new JsonResponse($data, 200);

	}

	/**
	 * @param $id
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteLogoAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);


		$om     = $this->getDoctrine()->getManager();
		$entity = $om->getRepository(Department::class)->find($id);

		if ($entity instanceof Department)
		{
			$file = $entity->getLogo();
			if (file_exists($file))
				unlink($file);

			$entity->setLogo(null);
			$om->persist($entity);
			$om->flush();
		}

		return $this->redirectToRoute('department_edit', ['id' => $id]);
	}
}
