<?php

namespace Busybee\RecordBundle\Model ;

class ListManager
{
	private $recordEntity;
	
	public function __construct( RecordEntityManager $recordEntity )
	{
		$this->recordEntity = $recordEntity;
	}
	
	public function getList($table, $data, $display)
	{
		$fields = $this->recordEntity->getFieldRepository()->findByTable($table);
		$dataList = array();

		foreach($fields as $field)
			if ($data === $field->getId())
			{
				$data = $field;
				$dataList = $this->recordEntity->getContainer()->get('record.'.$data->getType().'.repository')->findBy(array('field' => $data->getId()), array('record' => 'ASC'));
				break;
			}
		if (empty($dataList))
				$dataList = $this->recordEntity->getContainer()->get('record.repository')->findBy(array('table' => $table->getId()), array('record' => 'ASC'));
		if (! is_array($display)) 
		{
			$x = $display;
			$display = array();
			$display[] = $x;
		}
		$displayList = array();
		foreach($fields as $field)
			foreach($display as $q => $w)
				if ($w === $field->getId())
				{
					$display[$q] = $field;
					$displayList[$q] = $this->recordEntity->getContainer()->get('record.'.$display[$q]->getType().'.repository')->findBy(array('field' => $display[$q]->getId()), array('record' => 'ASC'));
					break;
				}
			

		$result = array();
		foreach ($dataList as $data)
		{
			$prompt = '';
			$rec_id = 0;

			foreach ($displayList as $w)
			{
				foreach($w as $display)
				{
					$prompt .= $display->valueToString().' ';
					if (empty($rec_id))
						$rec_id = $display->getRecord();
				}
			}
			$prompt = trim($prompt);

			$found = false;

			if ($rec_id === $data->getRecord())
			{
				$found = true;
				$result[$prompt] = $data->getValue();
				break ;
			}
			if (! $found)
				$result[$prompt] = $data->getValue();
		}

		return $result;
	}
}