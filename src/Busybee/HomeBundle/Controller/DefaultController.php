<?php
namespace Busybee\HomeBundle\Controller;

use Busybee\HomeBundle\Model\VersionManager;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse ;

class DefaultController extends Controller
{
    use \Busybee\SecurityBundle\Security\DenyAccessUnlessGranted ;

    /**
     * Load fixtures for all bundles
     *
     * @param Kernel $kernel
     */
    private static function loadFixtures(Kernel $kernel)
    {
        $loader = new DataFixturesLoader($kernel->getContainer());

        $em = $kernel->getContainer()->get('doctrine')->getManager();

        foreach ($kernel->getBundles() as $bundle) {
            $path = $bundle->getPath().'/DataFixtures/ORM';

            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }

        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException('Could not find any fixtures to load in');
        }
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures, true);
    }

    public function indexAction(Request $request )
    {
        $setting = $this->get('setting.manager');
        try {
            if (!$setting->has('Installed', true) || !$setting->get('Installed', false)) {
                $this->get('session')->invalidate();
                return new RedirectResponse($this->generateUrl('install_start'));
            }
        } catch (\Exception $e) {
            $this->get('session')->invalidate();
            return new RedirectResponse($this->generateUrl('install_start'));
        }

        $config = new \stdClass();
        $config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

        $user = $this->getUser();

        $tm = $this->get('timetable.display.manager');

        if (! is_null($user)) {
            $encoder = $this->get('security.encoder_factory');
            $encoder = $encoder->getEncoder($user);

            $identifier = $this->get('session')->has('tt_identifier') ? $this->get('session')->get('tt_identifier') : $tm->getTimeTableIdentifier($this->getUser());

            $displayDate = $this->get('session')->has('tt_displayDate') ? $this->get('session')->get('tt_displayDate') : $tm->getTimeTableDisplayDate();

            $tm->generateTimeTable($identifier, $displayDate);

            if ($encoder->isPasswordValid($user->getPassword(), 'p@ssword', $user->getSalt()) || $user->getExpired()) {
                $email = null;
                if (!empty($user))
                    $email = trim($user->getEmail());

                $config = new \stdClass();
                $config->signin = $this->get('security.failure.repository')->testRemoteAddress($request->server->get('REMOTE_ADDR'));

                return $this->render('BusybeeSecurityBundle:User:request.html.twig', array(
                    'email' => $email,
                    'config' => $config,
                    'forcePasswordReset' => $user->getExpired(),
                ));
            }
        }

        return $this->render('BusybeeHomeBundle::home.html.twig', array('config' => $config,
            'manager' => $tm,
        ));
    }

    public function acknowledgementAction()
    {
        $versions = $this->get('version.manager')->getVersion();

        return $this->render('@BusybeeHome/Acknowledgement/acknowledgement.html.twig',
            [
                'versions' => $versions,
            ]
        );
    }
}
