<?php

namespace Busybee\Core\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SpecialDayController extends Controller
{
	use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

	public function deleteAction($id, $year)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$sday = $this->get('specialDay.repository')->find($id);


		if ($sday->canDelete())
		{
			$em = $this->get('doctrine')->getManager();
			$em->remove($sday);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',
				$this->get('translator')->trans(
					'year.specialday.delete.success',
					[
						'%name%' => $sday->getDay()->format('jS M, Y'),
					],
					'BusybeeInstituteBundle')
			);
		}
		else
		{
			$this->get('session')->getFlashBag()->add(
				'warning',
				$this->get('translator')->trans(
					'year.specialday.delete.warning',
					[
						'%name%' => $sday->getDay()->format('jS M, Y'),
					],
					'BusybeeInstituteBundle')
			);
		}

		return new RedirectResponse($this->generateUrl('year_edit', ['id' => $year, '_fragment' => 'specialDays']));
	}
}
