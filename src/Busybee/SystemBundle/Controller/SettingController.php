<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\Form\FormError ;

class SettingController extends Controller
{
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$up = $this->get('setting.pagination');
		
		$up->injectRequest($request);
		
		$up->getDataSet();

        return $this->render('SystemBundle:Setting:manage.html.twig', 
			array(
            	'pagination' => $up,
        	)
		);
        return $this->render('SystemBundle:Setting:manage.html.twig');
	}


    public function editAction($id, Request $request)
    {
		$setting = $this->get('setting.repository')->findOneBy(array('id' => $id));
dump($setting);
		$this->denyAccessUnlessGranted($setting->getRole()->getRole(), null, 'Unable to access this page!');

		$sm = $this->get('setting.manager');

		$data = $request->request->get('setting');
		$valid = true ;
		
		if (! is_null($data))
		{
			switch ($setting->getType()) 
			{
				case 'array':
					try {
						$x = Yaml::parse($data['value']);
					} catch (\Exception $e) {
						$errorMsg = $e->getMessage();
						$valid = false;
					}
					break;
			}
		}
	
		$setting->cancelURL = $this->generateUrl('setting_manage');

		$form = $this->createForm('Busybee\SystemBundle\Form\SettingType', $setting);

		$options = array(
			'label' => 'system.setting.label.value',
		);

		switch ($setting->getType()) 
		{
			case 'boolean':
				$form->add('value', 'Busybee\FormBundle\Type\YesNoType', array_merge($options, array(
							'data' => $sm->get($setting->getName()),
							'help' => 'system.setting.help.boolean'
						)
					)
				);
				break ;
			case 'array':
				$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array_merge($options, array(
							'attr' => array(
								'help' => 'system.setting.help.array',
								'rows' => 8,
							),
							
						)
					)
				); 
				break ;
			case 'twig':
				$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array_merge($options, array(
							'attr' => array(
								'help' => 'system.setting.help.twig',
								'rows' => 8,
							),
							
						)
					)
				); 
				break ;
			case 'string':
				$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array_merge($options, array(
							'attr' => array(
								'maxLength' => 25,
							),
							
						)
					)
				); 
				break ;
			default:
				dump($setting);
				die();
		}

		$form->handleRequest($request);
		if (! $valid)
		{
			$form->get('value')->addError(new FormError($errorMsg));
		}
		
		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($setting);
			$em->flush();
		} 

        return $this->render('SystemBundle:Setting:edit.html.twig', array(
				'form' => $form->createView(),
			)
		);
	}
}