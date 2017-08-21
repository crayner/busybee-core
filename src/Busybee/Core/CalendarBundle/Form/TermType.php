<?php

namespace Busybee\Core\CalendarBundle\Form;

use Busybee\Core\CalendarBundle\Entity\Term;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\SecurityBundle\Form\DataTransformer\EntityToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityManager as ObjectManager;

class TermType extends AbstractType
{
	/**
	 * @var    ObjectManager
	 */
	private $om;

	/**
	 * Construct
	 */
	public function __construct(ObjectManager $manager)
	{
		$this->om = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$year  = $options['year_data'];
		$years = array();
		if (!is_null($year->getFirstDay()))
		{
			$years[] = $year->getFirstDay()->format('Y');
			if ($year->getFirstDay()->format('Y') !== $year->getLastDay()->format('Y'))
				$years[] = $year->getLastDay()->format('Y');
		}
		else
			$years[] = date('Y');
		$builder
			->add('name', null,
				array(
					'label' => 'term.label.name',
					'attr'  => array(
						'help' => 'term.help.name',
					),
				)
			)
			->add('nameShort', null,
				array(
					'label' => 'term.label.nameShort',
					'attr'  => array(
						'help' => 'term.help.nameShort',
					),
				)
			)
			->add('firstDay', null,
				array(
					'label' => 'calendar.label.firstDay',
					'attr'  => array(
						'help' => 'calendar.help.firstDay',
					),
					'years' => $years,
				)
			)
			->add('lastDay', null,
				array(
					'label' => 'calendar.label.lastDay',
					'attr'  => array(
						'help' => 'calendar.help.lastDay',
					),
					'years' => $years,
				)
			)
			->add('year', HiddenType::class);

		$builder->get('year')
			->addModelTransformer(new EntityToStringTransformer($this->om, Year::class));
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Term::class,
				'translation_domain' => 'BusybeeInstituteBundle',
				'year_data'          => null,
				'error_bubbling'     => true,
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'term';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'term';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['year_data'] = $options['year_data'];
	}
}
