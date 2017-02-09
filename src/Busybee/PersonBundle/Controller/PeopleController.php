<?php

namespace Busybee\PersonBundle\Controller;

use Busybee\PersonBundle\Form\ImportType;
use Busybee\PersonBundle\Form\MatchImportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class PeopleController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $data = new \stdClass();
        $data->returnURL = $this->generateUrl('home_page');
        $data->action = $this->generateUrl('people_import_match');

        $form = $this->createForm(ImportType::class, $data);


        return $this->render('BusybeePersonBundle:People:import.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function matchAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $data = array();
        $data['returnURL'] = $this->generateUrl('people_import');

        $files = $request->files->get('import');

        $file = reset($files);

        $file = $this->get('file.uploader')->upload($file);

        $pm = $this->get('person.manager');
        $headerNames = $pm->getHeaderNames($file);

        $data['action'] = $this->generateUrl('people_import_data') ;
        $data['file'] = $file ;
        $data['fields'] = $headerNames ;
        $data['headerNames'] = $headerNames ;

        $data['destinationNames'] = $pm->getFieldNames();

        $form = $this->createForm(MatchImportType::class, $data);

        return $this->render('BusybeePersonBundle:People:importMatch.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dataAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR');

        $import = $request->get('import');

        $results = $this->get('person.manager')->importPeople($import, $request->getSession());

        return $this->render('BusybeePersonBundle:People:importResults.html.twig',
            array(
                'results'   => $results,
                'import'    => $import,
            )
        );

    }
}
