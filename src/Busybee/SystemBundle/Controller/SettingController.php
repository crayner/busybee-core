<?php

namespace Busybee\SystemBundle\Controller ;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\SystemBundle\Form\UploadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\Form\FormError ;
use InvalidArgumentException ;
use Busybee\FormBundle\Type\ImageType ;
use Busybee\FormBundle\Type\YamlArrayType ;
use Symfony\Component\HttpFoundation\RedirectResponse ;


class SettingController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

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
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');
        $setting = $this->get('setting.repository')->findOneByName($name);

        if (is_null($setting)) throw new InvalidArgumentException('The System setting of name: '.$name.' was not found');
        return $this->editAction($setting->getId(), $request);
    }

    public function editAction($id, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

        $setting = $this->get('setting.repository')->findOneById($id);

        if (is_null($setting->getRole())) $setting->setRole($this->get('security.role.repository')->findOneByRole('ROLE_SYSTEM_ADMIN'));

        if (is_null($setting)) throw new InvalidArgumentException('The System setting of identifier: '.$id.' was not found');
        $this->denyAccessUnlessGranted($setting->getRole(), null, null);

        $sm = $this->get('setting.manager');

        $data = $request->request->get('setting');
        $valid = true ;

        if (! is_null($data)) {
            switch ($setting->getType()) {
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
        $attr = array('class' => 'changeSetting');

        $constraints = array();
        if (! is_null($setting->getValidator()))
            $constraints[] = $this->get($setting->getValidator());
        switch ($setting->getType()) {
            case 'array':
                $constraints[] = new \Busybee\HomeBundle\Validator\Yaml();
                break ;
            case 'twig':
                $constraints[] = new \Busybee\HomeBundle\Validator\Twig();
                break ;
            case 'regex':
                $constraints[] = new \Busybee\HomeBundle\Validator\Regex();
                break ;
        }

        if (count($constraints) > 0) $options['constraints'] = $constraints;

        switch ($setting->getType()) {
            case 'boolean':
                $form->add('value', ToggleType::class, array_merge($options, array(
                            'data' 			=> $sm->get($setting->getName()),
                            'attr'          => array(
                                'help' 			=> 'system.setting.help.boolean',
                            )
                        )
                    )
                );
                break ;
            case 'image':
                $form->add('value', ImageType::class, array_merge($options, array(
                            'data' 	=> $sm->get($setting->getName()),
                            'attr'	=> array_merge($attr,
                                array(
                                    'help' => 'system.setting.help.image',
                                    'imageClass' => 'imageWidth200',
                                )
                            ),
                        )
                    )
                );
                break ;
            case 'array':
                $form->add('value', TextareaType::class, array_merge($options, array(
                            'attr'	=> array_merge($attr,
                                array(
                                    'help' => 'system.setting.help.array',
                                    'rows' => 8,
                                )
                            ),
                            'constraints' => array_merge(
                                $constraints,
                                array(
                                    new \Busybee\HomeBundle\Validator\Yaml(),
                                )
                            ),
                        )
                    )
                );
                break ;
            case 'twig':
                $form->add('value', TextareaType::class, array_merge($options, array(
                            'attr'	=> array_merge($attr,
                                array(
                                    'help' => 'system.setting.help.twig',
                                    'rows' => 5,
                                )
                            ),
                            'constraints' => array_merge(
                                $constraints,
                                array(
                                    new \Busybee\HomeBundle\Validator\Twig(),
                                )
                            ),
                        )
                    )
                );
                break ;
            case 'string':
                if (is_null($setting->getChoice()))
                    $form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array_merge($options, array(
                                'attr'	=> array_merge($attr,
                                    array(
                                        'maxLength' => 25,
                                    )
                                ),
                                'constraints' => $constraints,
                            )
                        )
                    );
                else
                    $form->add('value', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array_merge($options, array(
                                'choices' => $sm->getChoices($setting->getChoice()),
                                'constraints' => $constraints,
                                'attr' => $attr,
                            )
                        )
                    );
                break ;
            case 'regex':
                $form->add('value', TextareaType::class, array_merge($options, array(
                            'attr'	=> array_merge($attr,
                                array(
                                    'rows' => 5,
                                )
                            ),
                            'constraints' => array_merge(
                                $constraints,
                                array(
                                    new \Busybee\HomeBundle\Validator\Regex(),
                                )
                            ),
                        )
                    )
                );
                break ;
            case 'text':
                $form->add('value', 'Symfony\Component\Form\Extension\Core\Type\TextType', array_merge($options, array(
                            'constraints' => $constraints,
                            'attr'	=> $attr,
                        )
                    )
                );
                break ;
            case 'time':
                $form->add('value', 'Busybee\FormBundle\Type\TimeType', array_merge($options, array(
                            'data' 			=> $sm->get($setting->getName()),
                            'constraints' => $constraints,
                            'attr'	=> $attr,
                        )
                    )
                );
                break ;
            default:
                throw new InvalidArgumentException(sprintf("The setting type %s has not been defined.", $setting->getType()));
        }

        $form->handleRequest($request);

        if (! $valid) {
            $form->get('value')->addError(new FormError($errorMsg));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($setting);
            $em->flush();
            if ($setting->getType() == 'image')
                return new RedirectResponse($this->generateUrl('setting_edit', array('id' => $id)));
        }

        return $this->render('SystemBundle:Setting:edit.html.twig', array(
                'form' => $form->createView(),
                'fullForm' => $form,
                'setting_id' => $setting->getId(),
            )
        );
    }

    public function uploadAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN');

        $form = $this->createForm(UploadType::class, new \stdClass());

        $form->handleRequest($request);

        $errors = array();
        $error = 0;
        if ($form->isValid()) {
            $file = $form->get('file')->getData();
            $content = file_get_contents($file->getRealPath());
            unlink($file->getRealPath());
            $content = Yaml::parse($content);
            $sess = $request->getSession();
            if (empty($content['name']) || $content['name'] !== $file->getClientOriginalName()) {
                $sess->getFlashBag()
                    ->add('warning', array('upload.error.fileNameMatch' => array('%name%' => $file->getClientOriginalName())));;
            }
            if (empty($content['settings'])) {
                $sess
                    ->getFlashBag()
                    ->add('error', 'upload.error.settingsMissing');
            }
            if (empty($errors)) {
                $sm = $this->get('setting.manager');
                foreach($content['settings'] as $name=> $value)
                    $sm->set($name, $value);
                $sess
                    ->getFlashBag()
                    ->add('success', array('upload.success.settings' => array('%count%' => count($content['settings']))));
                if ($form->get('default')) {
                    $exists = $this->get('setting.manager')->get('Settings.Default.Overwrite');
                    if (!empty($exists) && file_exists($exists))
                        unlink($exists);
                    $path = str_replace('app/../', '', $this->getParameter('upload_path'));
                    $fName = $path . '/' . md5(uniqid()) . '.yml';
                    file_put_contents($fName, Yaml::dump($content));
                    $this->get('setting.manager')->set('Settings.Default.Overwrite', $fName);
                    $sess
                        ->getFlashBag()
                        ->add('success', 'default.success');
                }
            }


        }

        return $this->render('SystemBundle:Setting:upload.html.twig',
            array(
                'form'	=> $form->createView(),
            )
        );
    }

}