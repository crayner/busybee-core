<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\InstituteBundle\Entity\Campus;
use Busybee\InstituteBundle\Events\CampusSubscriber;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Busybee\SystemBundle\Setting\SettingManager ;
use Doctrine\ORM\EntityRepository ;

class CampusType extends AbstractType
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * Construct
     */
    public function __construct(SettingManager $sm)
    {
        $this->sm = $sm;
        $this->manager = $this->sm->getContainer()->get('doctrine')->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', null, array(
                    'label' => 'campus.label.identifier',
                    'attr' => array(
                        'help' => 'campus.help.identifier',
                        'class' => 'locationForm monitorChange',
                    ),
                )
            )
            ->add('name', null, array(
                    'label' => 'campus.label.name',
                    'attr' => array(
                        'help' => 'campus.help.name',
                        'class' => 'locationForm monitorChange',
                    ),
                )
            )
            ->add('postcode', null, array(
                    'label' => 'campus.label.postcode',
                    'attr' => array(
                        'help' => 'campus.help.postcode',
                        'class' => 'locationForm monitorChange',
                    ),
                )
            )
            ->add('territory', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                    'label' => 'campus.label.territory',
                    'required' => false,
                    'choices' => $this->sm->get('Address.TerritoryList'),
                    'attr' => array(
                        'help' => 'campus.help.territory',
                        'class' => 'locationForm monitorChange',
                    ),
                )
            )
            ->add('locality', null, array(
                    'label' => 'campus.label.locality',
                    'attr' => array(
                        'help' => 'campus.help.locality',
                        'class' => 'locationForm monitorChange',
                    ),
                )
            )
            ->add('country', $this->sm->get('CountryType'), array(
                    'label' => 'campus.label.country',
                    'attr' => array(
                        'help' => 'campus.help.country',
                        'class' => 'locationForm monitorChange',
                    ),
                )
            )
            ->add('locationList', EntityType::class, array(
                    'class' => Campus::class,
                    'attr' => array(
                        'class' => 'locationList changeRecord formChanged',
                    ),
                    'label' => '',
                    'mapped' => false,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'placeholder' => 'campus.placeholder.locations',
                    'required' => false,
                    'data' => $options['data']->getId(),
                )
            );
        $builder->get('locationList')
            ->addModelTransformer(new EntityToStringTransformer($this->manager, Campus::class));
        $builder->addEventSubscriber(new CampusSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Campus::class,
            'translation_domain' => 'BusybeeInstituteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'campus';
    }


}
