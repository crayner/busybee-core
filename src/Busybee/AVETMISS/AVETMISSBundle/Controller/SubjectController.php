<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Controller;

use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Busybee\AVETMISS\AVETMISSBundle\Entity\Subject;
use Busybee\Program\CurriculumBundle\Entity\Subject as SubjectCore;
use Busybee\AVETMISS\AVETMISSBundle\Form\SubjectType;
use Symfony\Component\HttpFoundation\Request;

class SubjectController extends BusybeeController
{


	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$subject = new Subject();
		$id      = $request->get('id');

		if (intval($id) > 0)
			$subject = $this->get('avetmiss.subject.repository')->findOneById($id);

		$subject->cancelURL = $this->get('router')->generate('avetmiss_subject_manage');

		$core = $subject->getSubject();
		if (is_null($core->getName())) $core = new SubjectCore();

		$subject->name    = $core->getName();
		$subject->version = $core->getVersion();
		$subject->core    = is_null($subject->getId()) ? null : $subject;

		$form = $this->createForm(SubjectType::class, $subject);

		if (intval($id) > 0)
		{
			$form->get('subject')->setData($id);
			$form->get('name')->setData($subject->getSubject()->getName());
		}

		$data = $request->request->get('avetmiss_subject');

		if (!empty($data['name'])) $form->get('name')->setData($data['name']);
		if (!empty($data['version'])) $form->get('version')->setData($data['version']);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$subject->getSubject()->setName($data['name']);
			$subject->getSubject()->setVersion($data['version']);

			$em->persist($subject);
			$em->flush();

		}

		return $this->render('BusybeeAVETMISSBundle:Subject:index.html.twig', array('form' => $form->createView()));
	}
}
