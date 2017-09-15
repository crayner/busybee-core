<?php

namespace Busybee\Core\CalendarBundle\Model;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\SecurityBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

class YearManager
{
	/**
	 * @var ObjectManager
	 */
	private $manager;

	/**
	 * @var Form
	 */
	private $form;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * YearManager constructor.
	 *
	 * @param ObjectManager $manager
	 */
	public function __construct(ObjectManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @return ObjectManager
	 */
	public function getObjectManager()
	{
		return $this->manager;
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{


		$this->data = $event->getData();
		$this->form = $event->getForm();

		$year = $this->form->getData();

		if (isset($this->data['terms']) && is_array($this->data['terms']))
		{
			foreach ($this->data['terms'] as $q => $w)
			{
				$w['year']               = $year->getId();
				$this->data['terms'][$q] = $w;
			}
		}

		if (isset($this->data['specialDays']) && is_array($this->data['specialDays']))
		{
			foreach ($this->data['specialDays'] as $q => $w)
			{
				$w['year']                     = $year->getId();
				$this->data['specialDays'][$q] = $w;
			}
		}

		$event->setData($this->data);

		return $event;

	}

	/**
	 * @param $sql
	 * @param $rsm
	 */
	private function executeQuery($sql, $rsm)
	{

		$query = $this->manager->createNativeQuery($sql, $rsm);
		try
		{
			$query->execute();
		}
		catch (PDOException $e)
		{
			if (!in_array($e->getErrorCode(), ['1146']))
				throw new \Exception($e->getMessage());
		}
		catch (DriverException $e)
		{
			if (!in_array($e->getErrorCode(), ['1091']))
				throw new \Exception($e->getMessage());
		}

	}

	/**
	 * Can Delete
	 *
	 * @param Year $year
	 *
	 * @return bool
	 */
	public function canDelete(Year $year)
	{
		if (! $year->canDelete())
			return false;

		$users = $this->manager->getRepository(User::class)->createQueryBuilder('u')
			->leftJoin('u.year', 'y')
			->where('y.id = :year_id')
			->setParameter('year_id', $year->getId())
			->setMaxResults(1)
			->getQuery()
			->getResult();

		if (! empty($users))
			return false;

		return true;
	}
}