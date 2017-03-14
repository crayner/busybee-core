<?php

namespace Busybee\FormBundle\Type;

use Busybee\FormBundle\Events\SettingChoiceSubscriber;
use Busybee\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * SettingType constructor.
     *
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'setting';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            array(
                'setting_name',
            )
        );
        $resolver->setDefaults(
            array(
                'expanded' => false,
            )
        );
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new SettingChoiceSubscriber($this->settingManager));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['setting_name'] = $options['setting_name'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}