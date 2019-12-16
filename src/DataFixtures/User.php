<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class User extends Fixture
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
    public const RESTAURANT_ADMIN = 'ROLE_RESTAURANT_ADMIN';
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new \App\Entity\User();
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            '123'
        ));
        $admin->setRoles([self::ADMIN]);
        $admin->setUsername('admin');
        $manager->persist($admin);

        $manager->flush();
    }
}
