<?php

namespace Busybee\Core\SystemBundle\Controller;

use Busybee\Core\TemplateBundle\Type\SettingChoiceType;
use Busybee\Core\TemplateBundle\Type\TextType;
use Busybee\Core\TemplateBundle\Type\TimeType;
use Busybee\Core\TemplateBundle\Type\ToggleType;
use Busybee\Core\TemplateBundle\Validator\Integer;
use Busybee\Core\SystemBundle\Entity\Setting;
use Busybee\Core\SystemBundle\Form\CreateType;
use Busybee\Core\SystemBundle\Form\SettingType;
use Busybee\Core\SystemBundle\Form\UploadType;
use Busybee\Core\TemplateBundle\Controller\BusybeeController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\FormError;
use InvalidArgumentException;
use Busybee\Core\TemplateBundle\Type\ImageType;
use Busybee\Core\TemplateBundle\Type\YamlArrayType;
use Symfony\Component\HttpFoundation\RedirectResponse;


class SettingController extends BusybeeController
{


	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$up = $this->get('busybee_core_system.model.setting_pagination');

		$up->injectRequest($request);

		$up->getDataSet();

		return $this->render('SystemBundle:Setting:manage.html.twig',
			array(
				'pagination' => $up,
			)
		);

		return $this->render('SystemBundle:Setting:manage.html.twig');
	}

	/**
	 * @param         $name
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editNameAction($name, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');
		$setting = $this->get('busybee_core_system.repository.setting_repository')->findOneByName($name);

		if (is_null($setting)) throw new InvalidArgumentException('The System setting of name: ' . $name . ' was not found');

		return $this->editAction($setting->getId(), $request);
	}

	/**
	 * @param         $id
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction($id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$setting = $this->get('busybee_core_system.repository.setting_repository')->findOneById($id);

		if (is_null($setting->getRole())) $setting->setRole($this->get('security.role.repository')->findOneByRole('ROLE_SYSTEM_ADMIN'));

		if (is_null($setting)) throw new InvalidArgumentException('The System setting of identifier: ' . $id . ' was not found');
		$this->denyAccessUnlessGranted($setting->getRole(), null, null);

		$sm = $this->get('busybee_core_system.setting.setting_manager');

		$data  = $request->request->get('setting');
		$valid = true;

		if (!is_null($data))
		{
			switch ($setting->getType())
			{
				case 'array':
					try
					{
						$x = Yaml::parse($data['value']);
					}
					catch (\Exception $e)
					{
						$errorMsg = $e->getMessage();
						$valid    = false;
					}
					break;
			}
		}

		$setting->cancelURL = $this->generateUrl('setting_manage');
		$form               = $this->createForm(SettingType::class, $setting);

		$options = array(
			'label' => 'system.setting.label.value',
		);
		$attr    = array('class' => 'changeSetting');

		$constraints = array();
		if (!is_null($setting->getValidator()))
			$constraints[] = $this->get($setting->getValidator());
		switch ($setting->getType())
		{
			case 'array':
				$constraints[] = new \Busybee\Core\HomeBundle\Validator\Yaml();
				break;
			case 'twig':
				$constraints[] = new \Busybee\Core\HomeBundle\Validator\Twig();
				break;
			case 'regex':
				$constraints[] = new \Busybee\Core\HomeBundle\Validator\Regex();
				break;
		}

		if (count($constraints) > 0) $options['constraints'] = $constraints;

		switch ($setting->getType())
		{
			case 'boolean':
				$form->add('value', ToggleType::class, array_merge($options, array(
							'data' => $sm->get($setting->getName()),
							'attr' => array(
								'help' => 'system.setting.help.boolean',
							)
						)
					)
				);
				break;
			case 'integer':
				$form->add('value', NumberType::class, array_merge($options, array(
							'data'        => $sm->get($setting->getName()),
							'attr'        => array(
								'help' => 'system.setting.integer.help',
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new Integer(),
								)
							),
						)
					)
				);
				break;
			case 'image':
				$form->add('value', ImageType::class, array_merge($options, array(
							'data'        => $sm->get($setting->getName()),
							'attr'        => array_merge($attr,
								array(
									'help'       => 'system.setting.help.image',
									'imageClass' => 'mediumLogo',
								)
							),
							'fileName'    => 'setting',
							'deletePhoto' => 'ignore',
						)
					)
				);
				break;
			case 'file':
				$form->add('value', TextType::class, array_merge($options, array(
							'data' => $sm->get($setting->getName()),
							'attr' => array_merge($attr,
								array(
									'help' => 'system.setting.help.file',
								)
							),
						)
					)
				);
				break;
			case 'array':
				$form->add('value', TextareaType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								array(
									'help' => 'system.setting.help.array',
									'rows' => 8,
								)
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new \Busybee\Core\HomeBundle\Validator\Yaml(),
								)
							),
						)
					)
				);
				break;
			case 'twig':
				$form->add('value', TextareaType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								array(
									'help' => 'system.setting.help.twig',
									'rows' => 5,
								)
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new \Busybee\Core\HomeBundle\Validator\Twig(),
								)
							),
						)
					)
				);
				break;
			case 'system':
				$form->add('value', TextType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								[
									'maxLength' => 25,
									'readonly'  => 'readonly',
								]
							),
							'constraints' => $constraints,
						)
					)
				);
				break;
			case 'string':
				if (is_null($setting->getChoice()))
					$form->add('value', TextType::class, array_merge($options, array(
								'attr'        => array_merge($attr,
									array(
										'maxLength' => 25,
									)
								),
								'constraints' => $constraints,
							)
						)
					);
				else
					$form->add('value', SettingChoiceType::class, array_merge($options, [
								'setting_name' => $setting->getName(),
								'constraints'  => $constraints,
								'attr'         => $attr,
							]
						)
					);
				break;
			case 'regex':
				$form->add('value', TextareaType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								array(
									'rows' => 5,
								)
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new \Busybee\Core\HomeBundle\Validator\Regex(),
								)
							),
						)
					)
				);
				break;
			case 'text':
				$form->add('value', TextType::class, array_merge($options, array(
							'constraints' => $constraints,
							'attr'        => $attr,
						)
					)
				);
				break;
			case 'time':
				$form->add('value', TimeType::class, array_merge($options, array(
							'data'        => new \DateTime($sm->get($setting->getName())),
							'constraints' => $constraints,
							'attr'        => $attr,
						)
					)
				);
				break;
			default:
				throw new InvalidArgumentException(sprintf("The setting type %s has not been defined.", $setting->getType()));
		}

		$form->handleRequest($request);

		if (!$valid)
		{
			$form->get('value')->addError(new FormError($errorMsg));
		}

		if ($form->isSubmitted() && $form->isValid())
		{
			switch ($setting->getType())
			{
				case 'time':
					if ($setting->getValue() instanceof \DateTime)
						$setting->setValue($setting->getValue()->format('H:i:s'));
					break;
			}
			$em = $this->get('doctrine')->getManager();
			$em->persist($setting);
			$em->flush();
			$session                       = $this->get('session');
			$settings                      = $session->get('settings', []);

			switch ($setting->getType())
			{
				case 'array':
					$settings[$setting->getName()] = Yaml::parse($setting->getValue());
					break;
				default:
					$settings[$setting->getName()] = $setting->getValue();

			}

			$session->set('settings', $settings);

			if ($setting->getType() == 'image')
				return $this->redirectToRoute('setting_edit', array('id' => $id));
		}

		return $this->render('SystemBundle:Setting:edit.html.twig', array(
				'form'       => $form->createView(),
				'fullForm'   => $form,
				'setting_id' => $setting->getId(),
			)
		);
	}


	/**
	 * This action is only used by the program developer.
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

		$form = $this->createForm(CreateType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$create = $request->request->get('create');
			$data   = Yaml::parse($create['setting']);
			$sm     = $this->get('busybee_core_system.setting.setting_manager');
			foreach ($data as $name => $values)
			{
				$setting = new Setting();
				$setting->setName($name);
				foreach ($values as $field => $value)
				{
					$b = 'set' . ucfirst($field);
					if ($field == 'value' && is_array($value))
						$value = Yaml::dump($value);
					$setting->$b($value);
				}
				$sm->createSetting($setting);
			}
		}

		return $this->render('SystemBundle:Setting:create.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
			]
		);
	}
}