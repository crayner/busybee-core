<?php

namespace Busybee\SystemBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Busybee\SystemBundle\Form\DataTransformer\SettingNameTransformer ;
use Busybee\SystemBundle\Repository\SettingRepository ;

class SettingType extends AbstractType
{
	private $repo ;
	
	public function __construct(SettingRepository $repo)
	{
		$this->repo = $repo ;
	}
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('type', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
			->add('name', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
			->add('nameSelect', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', 
				array(
					'label' => 'system.setting.label.name',
					'choices' => $this->getSettingNameChoices(),
					'attr' => array(
						'help' => 'system.setting.help.name',
					),
					'mapped'	=> false,
					'data'	=> $options['data']->getNameSelect(),
				)
			)
			->add('displayName', null, 
				array(
					'label' => 'system.setting.label.displayName',
					'attr' => array(
						'help' => 'system.setting.help.displayName',
						'class' => 'changeSetting',
					)
				)
			)
			->add('description', TextareaType::class, 
				array(
					'label' => 'system.setting.label.description',
					'attr' => array(
						'help' => 'system.setting.help.description',
						'rows' => '5',
						'class' => 'changeSetting',
					)
				)
			)
		 	->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
				'label'					=> 'form.save',
				'translation_domain' 	=> 'BusybeeHomeBundle',
				'attr' 					=> array(
					'class'					=> 'btn btn-success glyphicons glyphicons-disk-save'
					),
				)
			)
			->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType', array(
					'label'					=> 'form.cancel', 
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'formnovalidate' 		=> 'formnovalidate',
						'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
						'onClick'				=> 'location.href=\''.$options['data']->cancelURL."'",
					),
				)
			)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
			array(
				'data_class' => 'Busybee\SystemBundle\Entity\Setting',
				'translation_domain' => 'BusybeeSystemBundle',
				'validation_groups' => array('Default'),
			)
		);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setting';
    }

	private function getSettingNameChoices()
	{
		$names = array();
		$settings = $this->repo->findBy(array(), array('name'=>'ASC'));
		foreach($settings as $setting)
			$names[$setting->getName()] = $setting->getId();
		return $names ;
	}
}
