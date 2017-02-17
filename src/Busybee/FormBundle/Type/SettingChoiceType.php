<?php

namespace Busybee\FormBundle\Type;

use Busybee\FormBundle\Events\SettingChoiceSubscriber;
use Busybee\SystemBundle\Setting\SettingManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingChoiceType extends AbstractType
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
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'setting';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            array(
                'settingName',
            )
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new SettingChoiceSubscriber($this->settingManager));
    }
}