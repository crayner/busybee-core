<?php
namespace Busybee\Core\HomeBundle\Controller;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ErrorController extends Controller
{
	public function indexAction(FlattenException $exception, Logger $logger = null)
	{

		if (in_array($this->getParameter('kernel.environment'), ['dev']))
		{
			$tokens = $this->get('profiler')->find('', '', 1, '', '', '');

			return new RedirectResponse($this->generateUrl('_profiler_exception', ['token' => $tokens[0]['token']]));

		}

		return $this->render('BusybeeHomeBundle:Error:index.html.twig', ['exception' => $exception]);
	}

}
