<?php

namespace Busybee\People\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Busybee\People\PersonBundle\Entity\Locality;

class PhoneController extends Controller
{
	use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function fetchAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$start   = $request->query->get('term');
		$results = $this->get('phone.repository')->createQueryBuilder('p')
			->select('p.phoneNumber')
			->where('p.phoneNumber LIKE :start')
			->setParameter('start', $start . '%')
			->orderBy('p.phoneNumber', 'ASC')
			->getQuery()
			->getResult();

		$phones = array();

		foreach ($results as $w)
			$phones[] = $w['phoneNumber'];

		return new JsonResponse(
			$phones,
			200
		);
	}
}
