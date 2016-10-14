<?php
// src/General/SecurityBundle/DataFixtures/ORM/LoadUserData.php

namespace Busybee\SecurityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\SecurityBundle\Entity\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    private $entity;
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->entity = $this->findOrCreateUser('admin', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->setUsernameCanonical('admin');
			$this->entity->setPassword('$2a$12$2sWSdbciRFcuP8SnFzj10uFyNiS88mKISWy3m985RyWXe.Wu8Myp2'); //p@ssword
			$this->entity->setEmail('admin@yourbusybeesite.com');
			$this->entity->setEmailCanonical('admin@yourbusybeesite.com');
			$this->entity->setLocked(false);
			$this->entity->setExpired(false);
			$this->entity->setCredentialsExpired(false);
			$this->entity->setEnabled(true);
			$repos = $manager->getRepository('BusybeeSecurityBundle:Role');
			$this->entity->addDirectrole($repos->findOneBy(['role' => 'ROLE_SYSTEM_ADMIN']));
	
			$manager->persist($this->entity);
			$manager->flush(); 
		}
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }

  /**
     * Helper method to return an already existing User from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $manager
     *
     * @return Group
     */
    protected function findOrCreateUser($name, ObjectManager $manager)
    {
        return $manager->getRepository('BusybeeSecurityBundle:User')->findOneBy(['username' => $name]) ?: new User($name);
    }

}