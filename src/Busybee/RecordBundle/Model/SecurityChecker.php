<?php

namespace Busybee\RecordBundle\Model ;

use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;


class SecurityChecker
{
	private $authorisation;
	private $tableRepository;
	private $session;
	private $router;
	private $translator ;

	public function __construct( \Busybee\SecurityBundle\Model\Authorisation $authorisation, \Busybee\DatabaseBundle\Entity\TableRepository $tableRepository, Container $container)
	{
		$this->authorisation = $authorisation;
		$this->tableRepository = $tableRepository;
		$this->session = $container->get('session');
		$this->translator = $container->get('translator');
		$this->router = $container->get('router');	
	}
	
	private function getTableRole($table)
	{
		$tableName = $table;
		$table = $this->tableRepository->findOneBy(array('name' => $table));
		if (empty($table))
			return NULL;
		else
			return $table->getSelectRole()->getRole();
	}
		
	
	public function testAuthorisation($table)
	{
		if (NULL === ($role = $this->getTableRole($table)))
		{
			$role = '';
            $url = $this->router->generate('busybee_home_page');
			$this->session->getFlashBag()->add(
				'warning',
				$this->translator->trans('security.authorisation.no_table', array("%name%" => $table), 'BusybeeSecurityBundle')
			);
            $response = new RedirectResponse($url);
            return $response;            
		}

		return $this->authorisation->redirectAuthorisation($role);
	}

	public function textAjaxAuthorisation($role, $request)
	{
		if (NULL === ($role = $this->getTableRole($table)))
		{
			if ($request->getMethod() !== "POST")
				return $this->testAuthorisation($role);
			$role = '';
            $url = $this->router->generate('busybee_home_page');
			$this->session->getFlashBag()->add(
				'warning',
				$this->translator->trans('security.authorisation.no_table', array("%name%" => $table), 'BusybeeSecurityBundle')
			);
			$response = new JsonResponse(
				array(
					'form' => $this->templating->render('BusybeeSecurityBundle:Ajax:login_content.html.twig',
						array(
							'redirect'			=> $url,
						)
					)
				), 200 
			);
			return $response;
		}
		return $this->authorisation->ajaxAuthorisation($role, $request);
	}

}