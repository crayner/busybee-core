<?php

namespace Busybee\InstituteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TermController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted;

    public function deleteAction($id, $year)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $repo = $this->get('term.repository');

        $term = $repo->find($id);

        if ($term->canDelete()) {
            $em = $this->get('doctrine')->getManager();
            $em->remove($term);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans(
                    'year.term.delete.success',
                    [
                        '%name%' => $term->getName(),
                    ],
                    'BusybeeInstituteBundle')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'warning',
                $this->get('translator')->trans(
                    'year.term.delete.warning',
                    [
                        '%name%' => $term->getName(),
                    ],
                    'BusybeeInstituteBundle')
            );
        }
        return new RedirectResponse($this->generateUrl('year_edit', ['id' => $year, '_fragment' => 'terms']));
    }
}
