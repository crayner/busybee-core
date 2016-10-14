<?php
// src/General/SecurityBundle/DataFixtures/ORM/LoadroleData.php

namespace Busybee\SecurityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\SecurityBundle\Entity\Role;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    private $entity;
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
		$repos = $manager->getRepository('BusybeeSecurityBundle:Role');

        $this->entity = $this->findOrCreateRole('ROLE_USER', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$manager->persist($this->entity);
			$manager->flush();
		}

        $this->entity = $this->findOrCreateRole('ROLE_ALLOWED_TO_SWITCH', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_PARENT', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_STUDENT', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_TEACHER', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_STUDENT')));
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_ALLOWED_TO_SWITCH')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_HEAD_TEACHER', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_TEACHER')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_PRINCIPAL', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_HEAD_TEACHER')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_ADMIN', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_PARENT')));
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_ALLOWED_TO_SWITCH')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_REGISTRAR', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_PRINCIPAL')));
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_ADMIN')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_SYSTEM_ADMIN', $manager);
       	if (intval($this->entity->getId()) == 0) {
			$this->entity->addChildrenRole($repos->findOneBy(array('role' => 'ROLE_REGISTRAR')));
			$manager->persist($this->entity);
			$manager->flush();
		}
		
        $this->entity = $this->findOrCreateRole('ROLE_STAFF', $manager);
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
        return 1;
    }
	
  /**
     * Helper method to return an already existing Role from the database, else create and return a new one
     *
     * @param string        $name
     * @param ObjectManager $manager
     *
     * @return Role
     */
    protected function findOrCreateRole($role, ObjectManager $manager)
    {
        return $manager->getRepository('BusybeeSecurityBundle:Role')->findOneBy(['role' => $role]) ?: new Role($role);
    }

}