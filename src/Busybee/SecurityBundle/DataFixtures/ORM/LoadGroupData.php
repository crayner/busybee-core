<?php
// src/General/SecurityBundle/DataFixtures/ORM/LoadGroupData.php

namespace Busybee\SecurityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\SecurityBundle\Entity\Group;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    private $entity;
	/**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

		$repos = $manager->getRepository('BusybeeSecurityBundle:Role');


        $this->entity = $this->findOrCreateGroup('Parent', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_PARENT')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Student', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_STUDENT')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Teaching Staff', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_TEACHER')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Non Teaching Staff', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addRole($repos->findOneBy(array('role' => 'ROLE_STAFF')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateGroup('Contact', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$manager->persist($this->entity);
			$manager->flush();
		}
		
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }

  /**
     * Helper method to return an already existing Group from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $manager
     *
     * @return Group
     */
    protected function findOrCreateGroup($name, ObjectManager $manager)
    {
        return $manager->getRepository('BusybeeSecurityBundle:Group')->findOneBy(['groupname' => $name]) ?: new Group($name);
    }

}