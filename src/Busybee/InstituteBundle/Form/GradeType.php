<?php

namespace Busybee\InstituteBundle\Form;

use Busybee\FormBundle\Type\SettingChoiceType;
use Busybee\InstituteBundle\Entity\Grade;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GradeType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * DepartmentType constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('grade', SettingChoiceType::class,
                [
                    'label' => 'grade.label.grade',
                    'setting_name' => 'student.groups',
                    'required' => true,
                    'placeholder' => 'grade.placeholder.grade'
                ]
            )
            ->add('year', HiddenType::class)
            ->add('sequence', HiddenType::class);

        $builder->get('year')->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Grade::class,
                'translation_domain' => 'BusybeeInstituteBundle',
                'year_data' => null,
                'error_bubbling' => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'grade';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['year_data'] = $options['year_data'];
    }
}
