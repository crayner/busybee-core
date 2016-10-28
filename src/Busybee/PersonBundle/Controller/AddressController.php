<?php

namespace Busybee\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse ;

class AddressController extends Controller
{
    public function checkAction(Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$address = array();
		$address['line1'] = $request->request->get('line1');
		$address['line2'] = $request->request->get('line2');
		$address['locality'] = $request->request->get('locality');
		$address['territory'] = $request->request->get('territory');
		$address['postCode'] = $request->request->get('postCode');
		$address['country'] = $request->request->get('country');

		return new JsonResponse(
				$this->get('address.manager')->testAddress($address), 
				200
			);
	
    }
}
