<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model\Report;


use Busybee\Core\SystemBundle\Model\ErrorManager;
use Busybee\AVETMISS\AVETMISSBundle\Entity\Report as ReportEntity;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

abstract class Report implements ReportInterface
{
	protected $container;

	protected $sm;

	protected $status;

	protected $errors;

	protected $lastCopy;

	protected $vetRepo;

	protected $parameters;

	protected $upload_path;

	protected $filePath;

	protected $fileLength;

	protected $year;

	protected $router;

	public function __construct(Container $container)
	{
		$this->container   = $container;
		$this->sm          = $container->get('busybee_core_system.setting.setting_manager');
		$this->errors      = new ErrorManager();
		$this->status      = 'warning';
		$this->em          = $container->get('doctrine')->getManager();
		$this->vetRepo     = $this->em->getRepository('BusybeeAVETMISSBundle:Report');
		$this->parameters  = $container->getParameter('AVETMISS Report');
		$this->upload_path = $container->getParameter('upload_path');
		$this->year        = date('Y', strtotime('now'));
	}

	public function retrieveLastReport($year)
	{
		$this->year = $year;

		return $this->getLastReport();
	}

	protected function getLastReport()
	{
		$this->report = $this->vetRepo->findOneBy(array('name' => $this->reportName, 'year' => $this->year));
		if (is_null($this->report))
		{
			$this->report = new ReportEntity();
			$this->report->setYear($this->year);
			$this->report->setName($this->reportName);
			$this->errors->addError($this->errors->createError($this->reportName, 'No Report', 'report.error.notInitiated', '', ''));
		}

		return $this;
	}

	public function getReportName()
	{
		return $this->reportName;
	}

	public function getYear()
	{
		return $this->year;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	protected function normalise($type, $value, $length)
	{
		return $this->$type($value, $length);
	}

	protected function writeFile()
	{
		if (!is_dir($this->upload_path))
			mkdir($this->upload_path, '0755', true);

		$this->filePath   = $this->upload_path . '/' . $this->reportName . '.txt';
		$this->fileLength = file_put_contents($this->filePath, $this->fileString);
		if ($this->fileLength)
		{
			unset($this->fileString);
			$this->writeRecord();
		}
	}

	protected function writeRecord()
	{
		$report = $this->report;
		$report->setStatus($this->status);
		$report->setErrors($this->errors);
		$report->setFilePath($this->filePath);
		$report->setFileLength($this->fileLength);

		$this->em->persist($report);
		$this->em->flush();

		$this->report = $report;

		return $this;
	}

	protected function testValue($w, $report, $q, $value, $route)
	{
		$ok = true;
		if (!empty($w['minLength']) && strlen($value) < $w['minLength'])
		{
			$this->errors->addError($this->errors->createError($report, $q, 'report.error.minLength', $value, $route));
			$this->status = 'danger';
			$ok           = false;
		}
		if (empty($w['blank']) && !empty($w['default'])) $value = $w['default'];
		if ((empty($w['blank']) || $w['blank'] === 'true') && empty($value))
		{
			$this->errors->addError($this->errors->createError($report, $q, 'report.error.blank', $value, $route));
			$this->status = 'danger';
			$ok           = false;
		}
		if (isset($w['values']) && is_array($w['values']) && !in_array($value, $w['values']))
		{
			$this->errors->addError($this->errors->createError($report, $q, 'report.error.invalid', $value, $route));
			$this->status = 'danger';
			$ok           = false;
		}
		if (isset($w['values']) && is_string($w['values']) && 0 === strpos($w['values'], 'setting.') && !$this->in_array($value, $this->sm->get(substr($w['values'], 8))))
		{
			$this->errors->addError($this->errors->createError($report, $q, 'report.error.invalid', $value, $route));
			$this->status = 'danger';
			$ok           = false;
		}

		return $ok;
	}

	protected function in_array($needle, $haystack, $strict = false)
	{
		foreach ($haystack as $item)
		{
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array($needle, $item, $strict)))
			{
				return true;
			}
		}

		return false;
	}

	private function A($value, $length)
	{
		return str_pad(trim($value), $length, ' ', STR_PAD_RIGHT);
	}

	private function N($value, $length)
	{
		return str_pad(trim(strval(intval($value))), $length, '0', STR_PAD_LEFT);
	}

	private function D($value, $length = 8)
	{
		if ($value instanceof \Datetime)
			return $value->format('dmY');

		return '@@@@@@@@';
	}
}
