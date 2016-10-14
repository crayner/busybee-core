<?php

/*
 * This file is part of the FOSSecurityBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Busybee\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Busybee\SecurityBundle\Event\GetResponseRoleEvent;
use Busybee\SecurityBundle\BusybeeSecurityEvents;
use Busybee\SecurityBundle\Event\FormEvent;
use Busybee\SecurityBundle\Form\RoleType;
use Busybee\SecurityBundle\Entity\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Busybee\SecurityBundle\Event\FilterRoleResponseEvent;

class RoleController extends Controller {
    
    /**
     * Show all Roles
     */
    public function listAction()
    {
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_SYSTEM_ADMIN'))) return $response;
        
		$em = $this->getDoctrine()->getManager();
        $roles = $this->get('security.role_hierarchy');
        $childList = $roles->getAssigned();

        $roleNames = array();
        foreach ($roles->getMap() as $role => $children)
        {
            $roleNames[$role]['name'] = $role;
            $roleNames[$role]['children'] = $childList[$role];
            $roleNames[$role]['descendants'] = $children;
        }
        return $this->render('BusybeeSecurityBundle:Role:list.html.twig', array(
            'roles' => $roleNames,
        ));
    }
   /**
     * Edit a Role
     */
    public function editAction(Request $request, $roleName) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_SYSTEM_ADMIN'))) return $response;
   
        
		$em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Role');

        $role = $repository->findOneBy(array('role' => $roleName));

        $dispatcher = new EventDispatcher();

        $event = new GetResponseRoleEvent($role, $request);
        $dispatcher->dispatch(BusybeeSecurityEvents::ROLE_EDIT_INITIALISE, $event);


        $rh = $this->get('security.role_hierarchy');
   
        $rr = $rh->getHierarchy();
        
        $form = $this->createForm( 'Busybee\SecurityBundle\Form\RoleType', $role);

        $form->add('delete', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array('label' => 'form.delete',
                                'attr' => array('formnovalidate' => 'formnovalidate'),
                                'translation_domain' => 'BusybeeDisplayBundle',
                                'attr' => array('class' => 'btn btn-danger glyphicon glyphicon-remove-sign'),
                                ));

        $form->handleRequest($request);
        
        
        if ($form->get('delete')->isClicked()) {
            $url = $this->generateUrl('busybee_security_role_delete', array('roleName' => $roleName));
            $response = new RedirectResponse($url);
            return $response;            
        }
        if ($form->get('cancel')->isClicked()) {
            $url = $this->generateUrl('busybee_security_role_list');
            $response = new RedirectResponse($url);
            return $response;            
        }

     
        if ($form->isValid()) {


        
            $event = new FormEvent($form, $request);

            $dispatcher->dispatch(BusybeeSecurityEvents::ROLE_EDIT_SUCCESS, $event);
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($role);
            $em->flush();

            
            $url = $this->generateUrl('busybee_security_role_list');
            if ($form->get('save_and_add')->isClicked()) {
                $url = $this->generateUrl('busybee_security_role_add');
                $response = new RedirectResponse($url);
            } else
                $response = new RedirectResponse($url);
            
            $this->addFlash(
                'success',
                $this->get('translator')->trans('The role %rolename% was saved.', array('%rolename%' => $role->getRole()))
            );

            $dispatcher->dispatch(BusybeeSecurityEvents::ROLE_EDIT_COMPLETED, new FilterRoleResponseEvent($role, $request, $response));
            
            return $response;
        }
        
        return $this->render('BusybeeSecurityBundle:Role:edit.html.twig', array(
                'role' => $role,
                'form' => $form->createView(),
            ));
    }
   /**
     * Add a new Role
     */
    public function addAction(Request $request) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_SYSTEM_ADMIN'))) return $response;
 
        
		$em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Role');

        $role = new Role();
 
        $dispatcher = new EventDispatcher();

        $event = new GetResponseRoleEvent($role, $request);
        $dispatcher->dispatch(BusybeeSecurityEvents::ROLE_NEW_INITIALISE, $event);


        $form = $this->createForm('Busybee\SecurityBundle\Form\RoleType', $role);

        $form->handleRequest($request);
        
        if ($form->get('cancel')->isClicked()) {
            $url = $this->generateUrl('busybee_security_role_list');
            $response = new RedirectResponse($url);
            return $response;            
        }

     
        if ($form->isValid()) {
        
            $event = new FormEvent($form, $request);

            $dispatcher->dispatch(BusybeeSecurityEvents::ROLE_NEW_SUCCESS, $event);
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($role);
            $em->flush();

            $url = $this->generateUrl('busybee_security_role_list');
            if ($form->get('save_and_add')->isClicked()) {
                $url = $this->generateUrl('busybee_security_role_add');
                $response = new RedirectResponse($url);
            } else
                $response = new RedirectResponse($url);
            
            $this->addFlash(
                'success',
                $this->get('translator')->trans('The role %rolename% was created.', array('%rolename%' => $role->getRole()))
            );
            
            $dispatcher->dispatch(BusybeeSecurityEvents::ROLE_NEW_COMPLETED, new FilterRoleResponseEvent($role, $request, $response));
            
            return $response;
        }
        
        return $this->render('BusybeeSecurityBundle:Role:edit.html.twig', array(
                'role' => $role,
                'form' => $form->createView(),
            ));
    }
   /**
     * Delete a Role
     */
    public function deleteAction($roleName) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_SYSTEM_ADMIN'))) return $response;

        $repository = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Role');
        
        $role = $repository->findOneBy(array('role' => $roleName));
        $this->addFlash(
            'warning',
            $this->get('translator')->trans('The role %rolename% was deleted.', array('%rolename%' => $role->getRole()))
        );

        $em = $this->getDoctrine()->getManager();

        $em->remove($role);
        $em->flush();

        $url = $this->generateUrl('busybee_security_role_list');
        
        $response = new RedirectResponse($url);
        

        
        return $response;
    }
        
}