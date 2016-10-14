<?php

namespace Busybee\RecordBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Busybee\RecordBundle\Model\FormManager ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class EditController extends Controller
{
    public function indexAction($table_form, Request $request, $record_id)
    {
		if (true !== ($response = $this->get('record_security')->testAuthorisation($table_form))) return $response;
        
		$table = $this->get('table.repository')->findOneBy(array('name' => $table_form));

		if ($table->getLimits() === 'single' and intval($record_id) !== 1)
		{
			$url = $this->generateUrl('record_edit', array("table_form" => $table->getName(), "record_id" => 1));
            $response = new RedirectResponse($url);
			return $response ;

		}
		
		$fieldRepository = $this->get('field.repository');

		$fields = $fieldRepository->findByTable($table);
		
		$form = $this->createForm( 'Busybee\RecordBundle\Form\RecordType', $fields);
		
		$fm = $this->get('record.form.manager')->injector($form, $table, $fields, $record_id);
		
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
