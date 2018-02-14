<?php

namespace PasswordManager\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\GroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use PasswordManager\Bundle\UserBundle\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="PasswordManager\Bundle\UserBundle\Repository\UserRepository")
 */


class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToMany(targetEntity="PasswordManager\Bundle\UserBundle\Entity\Group")
     *
     */
    protected $groups;

    public function __construct()
    {
        parent::__construct();

        // your own logic
        $this->roles = array('ROLE_USER');



    }
}

