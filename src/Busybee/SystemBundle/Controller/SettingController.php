<?php

namespace Busybee\SystemBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\Form\FormError ;
use InvalidArgumentException ;

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


    public function editNameAction($name, Request $request)
    {
		$setting = $this->get('setting.repository')->findOneByName($name);

		if (is_null($setting)) throw new InvalidArgumentException('The System setting of name: '.$name.' was not found');
		return $this->editAction($setting->getId(), $request);
	}
	
    public function editAction($id, Request $request)
    {
		$setting = $this->get('setting.repository')->findOneById($id);

		if (is_null($setting)) throw new InvalidArgumentException('The System setting of identifier: '.$id.' was not found');
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
							'constraints' => array(
								new \Busybee\HomeBundle\Validator\Yaml(),
							),
						)
					)
				); 
				break ;
			case 'twig':
				$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array_merge($options, array(
							'attr' => array(
								'help' => 'system.setting.help.twig',
								'rows' => 5,
							),
							'constraints' => array(
								new \Busybee\HomeBundle\Validator\Twig(),
							),
						)
					)
				); 
				break ;
			case 'string':
				if (is_null($setting->getChoice()))
					$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array_merge($options, array(
							'attr' => array(
								'maxLength' => 25,
							),
						)
					)
				);
				else 
				{
					$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array_merge($options, array(
						'choices' => $sm->getChoices($setting->getChoice())
					))); 
				}
				break ;
			case 'regex':
				$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array_merge($options, array(
							'attr' => array(
								'rows' => 5,
							),
							'constraints' => array(
								new \Busybee\HomeBundle\Validator\Regex(),
							),
						)
					)
				); 
				break ;
			case 'text':
				$form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array_merge($options, array())); 
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
				'fullForm' => $form,
			)
		);
	}
	public function uploadAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
/*		$list = Yaml::parse(file_get_contents(__DIR__.'/../Resources/Defaults/Australia.yml'));
		$sm = $this->get('setting.manager');
		foreach($list as $name=>$value)
			$sm->set($name, $value);
*/
		$form = $this->createForm('Busybee\SystemBundle\Form\UploadType', new \stdClass());

		$form->handleRequest($request);

		$errors = array();
		$error = 0;
		if ($form->isValid())
		{
			$file = $form->get('file')->getData();
			$content = file_get_contents($file->getRealPath());
			unlink($file->getRealPath());
			$content = Yaml::parse($content);
			if (empty($content['name']) || $content['name'] !== $file->getClientOriginalName())
			{
				$errors[++$error]['message'] = 'upload.error.fileNameMatch';
				$errors[$error]['status'] = 'warning';
				$errors[$error]['options'] = array('%name%' => $file->getClientOriginalName());
			}
			if (empty($content['settings']))
			{
				$errors[++$error]['message'] = 'upload.error.settingsMissing';
				$errors[$error]['status'] = 'danger';
				$errors[$error]['options'] = array();
			}
			if (empty($errors)) {
				$sm = $this->get('setting.manager');
				foreach($content['settings'] as $name=>$value)
					$sm->set($name, $value);
				$errors[++$error]['message'] = 'upload.success';
				$errors[$error]['status'] = 'success';
				$errors[$error]['options'] = array('%count%' => count($content['settings']));
			}
		}

        return $this->render('SystemBundle:Setting:upload.html.twig',
			array(
				'form'	=> $form->createView(),
				'errors' => $errors,
			)
		);
	}

}