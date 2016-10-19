<?php
namespace Busybee\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Busybee\SecurityBundle\Security\Role\RoleHierarchy ;
use Busybee\SecurityBundle\Form\GroupType;
use Busybee\SecurityBundle\Entity\Group ;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class GroupController extends Controller
{
   /**
     * Add a new Group
     */
    public function newAction(Request $request) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

        $group = new Group;        
        $group->roleList = $this->userRoleList();
        
        $form = $this->createForm("Busybee\SecurityBundle\Form\GroupType", $group);

        $form->handleRequest($request);

        if ($form->get('cancel')->isClicked()) {
            $url = $this->generateUrl('busybee_security_group_list');
            $response = new RedirectResponse($url);
            return $response;            
        }

        if ($form->isValid()) {

            $repository = $this->getDoctrine()
                ->getRepository('BusybeeSecurityBundle:Group');
            
            $repository->saveGroup($group);
        
            $url = $this->generateUrl('busybee_security_group_list');
            if ($form->get('save_and_add')->isClicked()) {
                $url = $this->generateUrl('busybee_security_group_new');
                $response = new RedirectResponse($url);
            } else
                $response = new RedirectResponse($url);

            $this->addFlash(
                'success',
                $this->get('translator')->trans('The group %groupname% was created.', array('%groupname%' => $group->getGroupname()))
            );
        
            return $response;
        }
        
        return $this->render('BusybeeSecurityBundle:Group:new.html.twig', array(
                'form' => $form->createView(),
                'roleHierarchy' => $group->roleList,
            ));
    }
   /**
     * Edit a Group
     */
    public function editAction(Request $request, $GroupID) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;
		   
        $repository = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Group');

        $group = $repository->findOneBy(array('id' => $GroupID));
            
        $group->roleList = $this->userRoleList();
        
        $form = $this->createForm("Busybee\SecurityBundle\Form\GroupType", $group);

        $form->add('delete', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
					'label' 				=> 'form.delete',
					'translation_domain' 	=> 'BusybeeHomeBundle',
					'attr' 					=> array(
						'class' 				=> 'btn btn-danger glyphicon glyphicon-remove-sign',
						'formnovalidate' 		=> 'formnovalidate'
					)
				)
			)
		;


        $form->handleRequest($request);

        if ($form->get('delete')->isClicked()) {
            $url = $this->generateUrl('busybee_security_group_delete', array('GroupID' => $GroupID));
            $response = new RedirectResponse($url);
            return $response;            
        }
        if ($form->get('cancel')->isClicked()) {
            $url = $this->generateUrl('busybee_security_group_list');
            $response = new RedirectResponse($url);
            return $response;            
        }

        if ($form->isValid()) {

            $repository->saveGroup($group);
        
            $url = $this->generateUrl('busybee_security_group_list');
            if ($form->get('save_and_add')->isClicked()) {
                $url = $this->generateUrl('busybee_security_group_new');
                $response = new RedirectResponse($url);
            } else
                $response = new RedirectResponse($url);

            $this->addFlash(
                'success',
                $this->get('translator')->trans('The group %groupname% was saved.', array('%groupname%' => $group->getGroupname()))
            );
        
            return $response;
        }
        
        return $this->render('BusybeeSecurityBundle:Group:edit.html.twig', array(
                'form' => $form->createView(),
                'roleHierarchy' => $group->roleList,
            ));
    }
   /**
     * List Groups
     */
    public function listAction() {

		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

        $repository = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Group');

        $roleHierarchy = $this->get('security.role_hierarchy');
        
        $groups = $repository->findBy(array(), array('groupname' => 'ASC'));
        
        return $this->render('BusybeeSecurityBundle:Group:list.html.twig', array(
                'groups' => $groups,
                'hierarchy' => $roleHierarchy->getMap(),
            ));

    }
    /**
     * @return array
     */
    private function userRoleList() 
    { 
        $roleHierarchy = $this->get('security.role_hierarchy');
        $roleList = array();
		$roleRepos = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Role');
        foreach($roleHierarchy->getHierarchy() as $role => $w)
            if ($this->get('security.authorization_checker')->isGranted($role))
                $roleList[] = $roleRepos->findOneBy(array('role' => $role));
        return $roleList;
    }
   /**
     * Delete a Role
     */
    public function deleteAction($GroupID) {
    
		if (true !== ($response = $this->get('busybee_security.authorisation.checker')->redirectAuthorisation('ROLE_REGISTRAR'))) return $response;

        $repository = $this->getDoctrine()
            ->getRepository('BusybeeSecurityBundle:Group');
        
        $group = $repository->find($GroupID);
        $this->addFlash(
            'warning',
            $this->get('translator')->trans('The group %groupname% was deleted.', array('%groupname%' => $group->getGroupname()))
        );

        $em = $this->getDoctrine()->getManager();

        $em->remove($group);
        $em->flush();

        $url = $this->generateUrl('busybee_security_group_list');
        
        $response = new RedirectResponse($url);
        
        return $response;
    }
        
}
