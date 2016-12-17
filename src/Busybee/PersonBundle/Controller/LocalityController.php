<?php

namespace Busybee\PersonBundle\Controller ;

use Busybee\PersonBundle\Form\LocalityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request ;
use Busybee\PersonBundle\Entity\Locality ;

class LocalityController extends Controller
{
    public function indexAction($id, Request $request)
    {
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');
		
		$locality = new Locality();
		$lr = $this->get('locality.repository');
		if ($id !== 'Add')
			$locality = $lr->find($id);

		$locality->injectRepository($lr);

        $form = $this->createForm(LocalityType::class, $locality);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->get('doctrine')->getManager();
            $em->persist($locality);
            $em->flush();

            $sess =$request->getSession();
            $sess->getFlashBag()->add('success', 'locality.save.success');

            return new RedirectResponse($this->get('router')->generate('locality_manage', array('id' => $locality->getId())));
        }

        return $this->render('BusybeePersonBundle:Locality:index.html.twig',
			array('id' => $id, 'form' => $form->createView())			
		);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteAction($id = 0, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, 'Unable to access this page!');

        if ($id > 0 && $entity = $this->get('locality.repository')->find($id))
        {
            $entity->injectRepository($this->get('locality.repository'));
            $sess = $request->getSession();
            if ($entity->canDelete())
            {
                $em = $this->get('doctrine')->getManager();
                $em->remove($entity);
                $em->flush();

                $sess->getFlashBag()->add('success', 'locality.delete.success');
            } else
            {
                $sess->getFlashBag()->add('warning', 'locality.delete.notAllowed');
            }
        }

        return new RedirectResponse($this->get('router')->generate('locality_manage', array('id' => 'Add')));
    }
}
