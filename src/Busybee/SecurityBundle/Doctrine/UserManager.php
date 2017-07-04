<?php
namespace Busybee\SecurityBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Busybee\SecurityBundle\Model\UserInterface;
use Busybee\SecurityBundle\Model\UserManager as BaseUserManager;
use Busybee\SecurityBundle\Util\CanonicaliserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager extends BaseUserManager
{
    protected $objectManager;
    protected $class;
    protected $repository;
    protected $session;

    /**
     * Constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param CanonicaliserInterface  $usernameCanonicaliser
     * @param CanonicaliserInterface  $emailCanonicaliser
     * @param ObjectManager           $om
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicaliserInterface $canonicaliser, Session $session, ObjectManager $om, $class)
    {
        parent::__construct($encoderFactory, clone $canonicaliser, clone $canonicaliser);

        $this->objectManager = $om;
        $this->session = $session;

        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $this->objectManager->refresh($user);
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

    /**
     * Find Children
     *
     * @param $user
     * @param $checker
     * @return array
     */
    public function findChildren($user, $checker)
    {

		$users = array();
        foreach ($this->findUsers() as $test)
		{
			if ($test->getUsername() === $user->getUsername())
				continue ;
			$valid = true;
			$roles = $test->getRoles();

            foreach ($roles as $role)
			{
				if (! $checker->isGranted($role))
				{
					$valid = false;
					break ;
				}
			}
			if (! $valid)
				continue;
			$users[] = $test;
		}
		$x = array();
		foreach($users as $w) {
			$x[$w->getUsername()]['name'] = $w->getUsername();
			$x[$w->getUsername()]['id'] = $w->getID();
			$x[$w->getUsername()]['email'] = $w->getEmail();
		}
        ksort($x);
		return $x;
	}

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }
}
