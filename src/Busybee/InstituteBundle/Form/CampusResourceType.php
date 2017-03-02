<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\FormBundle\Type\ToggleType;
use Busybee\InstituteBundle\Entity\CampusResource;
use Busybee\InstituteBundle\Form\DataTransformer\CampusTransformer;
use Busybee\StaffBundle\Form\DataTransformer\StaffTransformer;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\StaffBundle\Entity\Staff;
use Symfony\Component\Form\Extension\Core\Type\IntegerType ;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Persistence\ObjectManager ;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampusResourceType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager ;

    /**
     * @var SettingManager
     */
    private $sm ;

    /**
     * Construct
     */
    public function __construct(ObjectManager $manager, SettingManager $sm)
    {
        $this->manager = $manager ;
        $this->sm = $sm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'label' => 'campus.resource.label.name',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('type', ChoiceType::class, array(
                    'choices' => $this->sm->get('Campus.Resource.Type'),
                    'label' => 'campus.resource.label.type',
                    'placeholder' => 'campus.resource.placeholder.type',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('capacity', IntegerType::class, array(
                    'label' => 'campus.resource.label.capacity',
                    'attr' => array(
                        'min' => '0',
                        'max' => '9999',
                        'help' => 'campus.resource.help.capacity',
                        'class' => 'monitorChange',
                    ),
                    'empty_data' => 0,
                )
            )
            ->add('computer', ToggleType::class, array(
                    'label' => 'campus.resource.label.computer',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('studentComputers', IntegerType::class, array(
                    'label' => 'campus.resource.label.studentComputers',
                    'attr' => array(
                        'min' => '0',
                        'max' => '999',
                        'class' => 'monitorChange',
                    ),
                    'empty_data' => 0,
                )
            )
            ->add('projector', ToggleType::class, array(
                    'label' => 'campus.resource.label.projector',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('tv', ToggleType::class, array(
                    'label' => 'campus.resource.label.tv',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('dvd', ToggleType::class, array(
                    'label' => 'campus.resource.label.dvd',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('hifi', ToggleType::class, array(
                    'label' => 'campus.resource.label.hifi',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('speakers', ToggleType::class, array(
                    'label' => 'campus.resource.label.speakers',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('iwb', ToggleType::class, array(
                    'label' => 'campus.resource.label.iwb',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('phoneInt', null, array(
                    'label' => 'campus.resource.label.phoneint',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('phoneExt', null, array(
                    'label' => 'campus.resource.label.phoneext',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('comment', TextareaType::class, array(
                    'label' => 'campus.resource.label.comment',
                    'required' => false,
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('staff1', EntityType::class,
                array(
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'choice_value' => 'id',
                    'label' => 'campus.resource.label.staff1',
                    'placeholder' => 'campus.resource.placeholder.staff',
                    'empty_data'    => null,
                    'attr' => array(
                        'help' => 'campus.resource.help.staff',
                        'class' => 'monitorChange',
                    ),
                    'required' => false,
                )
            )
            ->add('campus', EntityType::class,
                array(
                    'label' => 'campus.resource.label.campus',
                    'attr' => array(
                        'help' => 'campus.resource.help.campus',
                        'class' => 'monitorChange',
                    ),
                    'class' => 'Busybee\InstituteBundle\Entity\Campus',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'empty_data' => $this->manager->getRepository('BusybeeInstituteBundle:Campus')->find(1),
                    'placeholder' => 'campus.resource.placeholder.campus',
                )
            )
            ->add('staff2', EntityType::class,
                array(
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'choice_value' => 'id',
                    'label' => 'campus.resource.label.staff2',
                    'placeholder' => 'campus.resource.placeholder.staff',
                    'empty_data'    => null,
                    'attr' => array(
                        'help' => 'campus.resource.help.staff',
                        'class' => 'monitorChange',
                    ),
                    'required' => false,
                )
            )
            ->add('save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType',
                array(
                    'label' 				=> 'form.save',
                    'translation_domain' 	=> 'BusybeeHomeBundle',
                    'attr' 					=> array(
                        'class' 				=> 'btn btn-success glyphicons glyphicons-disk-save',
                    ),
                )
            )
            ->add('cancel', 'Symfony\Component\Form\Extension\Core\Type\ButtonType',
                array(
                    'label'					=> 'form.reset.button',
                    'translation_domain' 	=> 'BusybeeHomeBundle',
                    'attr' 					=> array(
                        'formnovalidate' 		=> 'formnovalidate',
                        'class' 				=> 'btn btn-info glyphicons glyphicons-remove-circle',
                        'onClick'				=> "location.href='".$options['data']->cancelURL."'",
                    ),
                )
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => CampusResource::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => 'campus.resource.placeholder.changeRecord',
                )
            )
        ;

        $builder->get('campus')->addModelTransformer(new CampusTransformer($this->manager));
        $builder->get('staff1')->addModelTransformer(new StaffTransformer($this->manager));
        $builder->get('staff2')->addModelTransformer(new StaffTransformer($this->manager));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Busybee\InstituteBundle\Entity\CampusResource',
            'translation_domain' => 'BusybeeInstituteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'busybee_institutebundle_campusresource';
    }


}
