<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * AppFixtures constructor.
     */
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $roles = [
            'ROLE_SUPERUSER' => 'Super Admin',
            'ROLE_EDITORIAL' => 'Manager',
            'ROLE_ADMINISTRATOR' => 'Admin',
            'ROLE_WRITER' => 'Redacteur',
        ];

        foreach ($roles as $key => $value) {
            if (!$manager->getRepository(Role::class)->findByRoleName([$key])) {
                $role = new Role();
                $role->setRoleName($key);
                $role->setLibelle($value);
                $manager->persist($role);
                $manager->flush();
            }
        }

        $user = new User();
        if (!$manager->find(User::class, 1)) {
            $user->setUsername('admin');
            $user->setRoles(['ROLE_SUPERUSER']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'));
            $user->setNomComplet('Admin');
            $user->setEmail('admin@example.com');
            $user->setValid(true);
            $user->setDeleted(false);
            $user->setAdmin(true);
            $manager->persist($user);

            $manager->flush();
        }
    }
}
