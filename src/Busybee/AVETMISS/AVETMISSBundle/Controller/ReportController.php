<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ReportController extends Controller
{
	use \Busybee\Core\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function indexAction(Request $request)
	{
		return $this->startAction($request);
	}

	public function startAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$report = array(
			'nat00005',
			'nat00010',
			'nat00020',
			'nat00030',
			'nat00060',
			'nat00080',
			'nat00085',
			'nat00090',
			'nat00100',
		);
		$result = array();
		foreach ($report as $name)
		{
			if ($request->get($name) === 'on')
			{
				$result[$name] = $this->get('avetmiss.report.' . $name)->execute($request->get('year'));
			}
			else
			{
				$result[$name] = $this->get('avetmiss.report.' . $name)->retrieveLastReport($request->get('year'));
			}
		}

		return $this->render('BusybeeAVETMISSBundle:Report:index.html.twig', array('result' => $result));
	}

	public function downloadAction($name, $year)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$report = $this->get('avetmiss.report.repository')->findOneBy(array('name' => $name, 'year' => $year));

		$path     = $this->get('kernel')->getRootDir() . '/' . $report->getFilePath();
		$response = new BinaryFileResponse($path);

		// Give the file a name:
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $report->getName() . '.txt');

		return $response;
	}
}
