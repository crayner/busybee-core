<?php

namespace Busybee\PersonBundle\Form ;

use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\SecurityBundle\Form\ResetType;
use Symfony\Component\Form\AbstractType ;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface ;
use Symfony\Component\OptionsResolver\OptionsResolver ;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\PersonBundle\Repository\LocalityRepository ;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ;


class LocalityType extends AbstractType
{
    /**
     * @var	SettingManager
     */
    private $sm ;
    /**
     * @var LocalityRepository
     */
    private $lr ;

    /**
     * Construct
     */
    public function __construct(SettingManager $sm, LocalityRepository $lr)
    {
        $this->sm = $sm ;
        $this->lr = $lr ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                    'label' => 'locality.label.name',
                    'attr' => array(
                        'class' => 'beeLocality monitorChange',
                        'help' => 'locality.help.name',
                    ),
                )
            )
            ->add('territory', SettingChoiceType::class, array(
                    'label' => 'locality.label.territory',
                    'attr' => array(
                        'class' => 'beeTerritory monitorChange',
                    ),
                    'setting_name' => 'Address.TerritoryList',
                    'placeholder' => 'locality.placeholder.territory',
                )
            )
            ->add('postCode', null, array(
                    'label' => 'locality.label.postcode',
                    'attr' => array(
                        'class' => 'beePostCode monitorChange',
                    ),
                )
            )
            ->add('country', CountryType::class, array(
                    'label' => 'locality.label.country',
                    'attr' => array(
                        'class' => 'beeCountry monitorChange',
                    ),
                )
            )
            ->add('localityList', EntityType::class,
                array(
                    'class' => 'BusybeePersonBundle:Locality',
                    'label' => 'locality.label.choice',
                    'choice_label'	=> 'fullLocality',
                    'placeholder' => 'locality.placeholder.choice',
                    'required' => false,
                    'attr' => array(
                        'help' => 'locality.help.choice',
                        'class' => 'beeLocalityList formChanged',
                    ),
                    'mapped' => false,
                    'translation_domain' => 'BusybeePersonBundle',
                    'query_builder' => function (LocalityRepository $lr) {
                        return $lr->createQueryBuilder('l')
                            ->orderBy('l.name', 'ASC')
                            ->addOrderBy('l.postCode', 'ASC');
                    },
                )
            )
            ->add('save', SubmitType::class, array(
                    'label'					=> 'form.save',
                    'attr' 					=> array(
                        'class' 				=> 'beeLocalitySave btn btn-success glyphicons glyphicons-plus-sign',
                    ),
                    'translation_domain' => 'BusybeeHomeBundle',
                )
            )
            ->add('close', ButtonType::class, array(
                    'label'					=> 'form.close',
                    'attr' 					=> array(
                        'class' 				=> 'formChanged beeLocalitySave btn btn-warning glyphicons glyphicons-folder-closed',
                        'onclick'               => "window.close()",
                    ),
                    'translation_domain'    => 'BusybeeHomeBundle',
                )
            )
            ->add('reset', ResetType::class, array(
                    'label'					=> 'form.reset',
                    'attr' 					=> array(
                        'class' 				=> 'beeLocalitySave btn btn-info glyphicons glyphicons-refresh',
                    ),
                    'translation_domain' => 'BusybeeHomeBundle',
                    'mapped'            => false,
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array (
                'data_class' => 'Busybee\PersonBundle\Entity\Locality',
                'translation_domain' => 'BusybeePersonBundle',
                'allow_extra_fields' => true,
                'classSuffix'	=> null,
                'csrf_protection' => false,			)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'locality';
    }
}
