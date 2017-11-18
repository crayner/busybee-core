<?php
namespace Busybee\Core\HomeBundle\Listener;

use Busybee\Core\SystemBundle\Model\FlashBagManager;
use Busybee\Core\SystemBundle\Model\MessageManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Router;

class KernelListener
{
	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	/**
	 * @var string
	 */
	private $router;

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @var FlashBagManager
	 */
	private $flashBagManager;

	/**
	 * KernelListener constructor.
	 *
	 * @param ObjectManager $objectManager
	 */
	public function __construct(ObjectManager $objectManager, Router $router, FlashBagManager $flashBagManager, $cacheDir)
	{
		$this->objectManager   = $objectManager;
		$this->router          = $router;
		$this->cacheDir        = $cacheDir;
		$this->flashBagManager = $flashBagManager;
	}

	/**
	 * on Kernel Exception
	 *
	 * @param GetResponseForExceptionEvent $event
	 */
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();

		if ($exception instanceof TableNotFoundException)
		{
			$pdoException = $exception->getPrevious();
			if ($pdoException instanceof PDOException)
			{
				$newEm = EntityManager::create($this->objectManager->getConnection(), $this->objectManager->getConfiguration());
				$meta  = $this->objectManager->getMetadataFactory()->getAllMetadata();

				$tool = new SchemaTool($newEm);
				$tool->createSchema($meta);
				$route = 'install_build_system';

				$url = $this->router->generate($route);

				$fs = new Filesystem();
				$fs->remove($this->cacheDir);

				sleep(2);

				$response = new RedirectResponse($url);
				$event->setResponse($response);
			}
		}

		if ($exception instanceof AccessDeniedHttpException)
		{

			$url = $this->router->generate('home_page');

			$mm = new MessageManager('SystemBundle');
			$mm->addMessage('danger', $exception->getMessage());

			$this->flashBagManager->addMessages($mm);

			$response = new RedirectResponse($url);
			$event->setResponse($response);
		}

		/*
				if ($exception instanceof \Twig_Error_Runtime)
				{
					dump($this);
					dump($exception);
					die();
				} */
	}
}