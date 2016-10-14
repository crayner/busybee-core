<?php

namespace Busybee\RecordBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\RecordBundle\Model\FormManager ;

class DisplayController extends Controller
{
    public function indexAction($table_form, $record_id, Request $request)
    {
		if (true !== ($response = $this->get('record_security')->testAuthorisation($table_form))) return $response;
        
		$table = $this->get('table.repository')->findOneBy(array('name' => $table_form));

		$fieldRepository = $this->get('field.repository');
		
		$fields = $fieldRepository->findByTableName($table_form);
		
		$form = $this->createForm($this->get('record.formtype'), $fields);
		
		$fm = $this->get('record.form.manager')->injector($form, $table, $fields, intval($record_id));
	
		$form = $fm->buildForm($fields);

        $form = $fm->handleRequest($request, $fields);

		return $this->render('BusybeeRecordBundle:Display:new.html.twig', array(
									'name'				=> $table_form,
									'form'				=> $form->createView(),
									'record'			=> $fm->createMetaData($table, $fields),
			)
		);
    }
}
