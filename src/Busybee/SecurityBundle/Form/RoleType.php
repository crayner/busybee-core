<?php
namespace Busybee\SecurityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType
{
    private $class;

    public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
				->add('role', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
						'label'					=>	'role.label.name',
					)
				)
				->add('childrenRoles', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array (
						'multiple' 				=> true,
						'expanded' 				=> true,
						'required' 				=> false,
						'choice_label' 			=> 'role',
						'class' 				=> 'Busybee\SecurityBundle\Entity\Role',
						'label' 				=> 'role.label.children',
					)
				)    
				->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
						'label' 				=> 'form.save',
						'translation_domain' 	=> 'BusybeeDisplayBundle',
						'attr' 					=> array(
							'class' 				=> 'btn btn-success glyphicon glyphicon-save'
						),
					)
				)
				->add('save_and_add', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
						'label' 				=> 'form.save_and_add',
						'translation_domain' 	=> 'BusybeeDisplayBundle',
						'attr' 					=> array(
							'class' 				=> 'btn btn-success glyphicon glyphicon-plus-sign'
						),
					)
				)
				->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
						'label'					=> 'form.cancel', 
						'translation_domain' 	=> 'BusybeeDisplayBundle',
						'attr' 					=> array(
							'class' 				=> 'btn btn-info glyphicon glyphicon-exclamation-sign',
							'formnovalidate' 		=> 'formnovalidate'
						),
					)
				)
    			;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'role',
			'translation_domain' => 'BusybeeSecurityBundle',
			'class' => 'Busybee\SecurityBundle\Entity\Role',
        ));
    }

    public function getName()
    {
        return 'bee_security_role';
    }
}
