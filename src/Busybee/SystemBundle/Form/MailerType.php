<?php

namespace Busybee\SystemBundle\Form;

use Busybee\FormBundle\Type\TextType;
use Busybee\SystemBundle\Event\MailerSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class MailerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transport', ChoiceType::class,
                [
                    'label' => 'mailer.transport.label',
                    'mapped' => false,
                    'choices' => [
                        'mailer.transport.placeholder' => 'off',
                        'mailer.transport.smtp' => 'smtp',
                        'mailer.transport.mail' => 'mail',
                        'mailer.transport.sendmail' => 'sendmail',
                        'mailer.transport.gmail' => 'gmail',
                    ],
                    'attr' => [
                        'help' => 'mailer.transport.help',
                    ],
                    'mapped' => false,
                ]
            )
            ->add('host', TextType::class,
                [
                    'label' => 'mailer.host.label',
                    'attr' => array(
                        'help' => 'mailer.host.help',
                        'class' => 'smtpMailer',
                    ),
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('port', TextType::class,
                [
                    'label' => 'mailer.port.label',
                    'attr' => array(
                        'help' => 'mailer.port.help',
                        'class' => 'smtpMailer',
                    ),
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->add('encryption', ChoiceType::class,
                [
                    'label' => 'mailer.encryption.label',
                    'mapped' => false,
                    'choices' => [
                        'mailer.encryption.none' => 'none',
                        'mailer.encryption.ssl' => 'ssl',
                        'mailer.encryption.tls' => 'tls',
                    ],
                    'attr' => [
                        'help' => 'mailer.encryption.help',
                        'class' => 'smtpMailer',
                    ],
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->add('auth_mode', ChoiceType::class,
                [
                    'label' => 'mailer.auth_mode.label',
                    'mapped' => false,
                    'choices' => [
                        'mailer.auth_mode.plain' => 'plain',
                        'mailer.auth_mode.login' => 'lodin',
                        'mailer.auth_mode.cram-md5' => 'cram-md5',
                    ],
                    'attr' => [
                        'help' => 'mailer.auth_mode.help',
                        'class' => 'smtpMailer',
                    ],
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->add('user', TextType::class,
                [
                    'label' => 'mailer.user.label',
                    'attr' => array(
                        'help' => 'mailer.user.help',
                        'class' => 'mailerDetails',
                    ),
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('password', TextType::class,
                [
                    'label' => 'mailer.password.label',
                    'attr' => array(
                        'help' => 'mailer.password.help',
                        'class' => 'mailerDetails',
                    ),
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('sender_name', TextType::class,
                [
                    'label' => 'mailer.sender_name.label',
                    'attr' => array(
                        'help' => 'mailer.sender_name.help',
                        'class' => 'mailerDetails',
                    ),
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('sender_address', EmailType::class,
                [
                    'label' => 'mailer.sender_address.label',
                    'attr' => array(
                        'help' => 'mailer.sender_address.help',
                        'class' => 'mailerDetails',
                    ),
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ],
                ]
            );
        $builder->addEventSubscriber(new MailerSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'SystemBundle',
            'data_class' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mailer';
    }


}
