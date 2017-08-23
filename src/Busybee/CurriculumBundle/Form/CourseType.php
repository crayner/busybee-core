<?php

namespace Busybee\CurriculumBundle\Form;

use Busybee\CurriculumBundle\Entity\Course;
use Busybee\CurriculumBundle\Events\CourseSubscriber;
use Busybee\Core\FormBundle\Type\SettingChoiceType;
use Busybee\Core\FormBundle\Type\TextType;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var array
     */
    private $studentGroups;

    /**
     * CourseType constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager, SettingManager $sm)
    {
        $this->manager = $manager;
        $this->studentGroups = $sm->get('student.groups');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                array(
                    'label' => 'course.label.name',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('code', TextType::class,
                array(
                    'label' => 'course.label.code',
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'course.help.code',
                    ),
                )
            )
            ->add('version', TextType::class,
                array(
                    'label' => 'course.label.version',
                    'attr' => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('targetYear', SettingChoiceType::class,
                array(
                    'label' => 'course.label.targetYear',
                    'attr' => array(
                        'class' => 'monitorChange',
                        'help' => 'course.help.targetYear',
                    ),
                    'placeholder' => 'course.placeholder.targetYear',
                    'multiple' => true,
                    'expanded' => true,
                    'setting_name' => 'student.groups',
                    'choice_translation_domain' => 'SystemBundle',
                )
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord',
                    ),
                    'class' => Course::class,
                    'choice_label' => 'fullName',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->addOrderBy('c.name', 'ASC')
                            ->addOrderBy('c.targetYear', 'ASC');
                    },
                    'placeholder' => 'course.placeholder.changeRecord',
                )
            );

        $builder->addEventSubscriber(new CourseSubscriber($this->studentGroups));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Course::class,
                'translation_domain' => 'BusybeeCurriculumBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'course';
    }


}
