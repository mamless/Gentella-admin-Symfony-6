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
            'ROLE_SUPERUSER',
            'ROLE_EDIT_USER',
            'ROLE_VIEW_USER',
            'ROLE_ADD_USER',
            'ROLE_ENABLE_USER',
            'ROLE_DELETE_USER',
            'ROLE_RESET_PASSWORD_USER',
            'ROLE_AG_DELETE_USER',
            'ROLE_AG_ENABLE_USER',
            'ROLE_EDIT_PROFILE',
            'ROLE_ADD_PROFILE',
            'ROLE_ENABLE_PROFILE',
            'ROLE_DELETE_PROFILE',
            'ROLE_AG_DELETE_PROFILE',
            'ROLE_AG_ENABLE_PROFILE',
            'ROLE_EDIT_CATEGORIE',
            'ROLE_ADD_CATEGORIE',
            'ROLE_ENABLE_CATEGORIE',
            'ROLE_DELETE_CATEGORIE',
            'ROLE_AG_DELETE_CATEGORIE',
            'ROLE_AG_ENABLE_CATEGORIE',
            'ROLE_ACCESS_MENU_MANAGE_GENERAL',
            'ROLE_EDIT_FAQ',
            'ROLE_ADD_FAQ',
            'ROLE_ENABLE_FAQ',
            'ROLE_DELETE_FAQ',
            'ROLE_AG_DELETE_FAQ',
            'ROLE_AG_ENABLE_FAQ',
            'ROLE_ACCESS_MENU_MANAGE_INBOX',
            'ROLE_EDIT_BLOG',
            'ROLE_ADD_BLOG',
            'ROLE_ENABLE_BLOG',
            'ROLE_DELETE_BLOG',
            'ROLE_AG_DELETE_BLOG',
            'ROLE_AG_ENABLE_BLOG',
            'ROLE_PREVIEW_BLOG',
            'ROLE_UNDO_HISTORYBLOG',
            'ROLE_VIEW_HISTORYBLOG'
        ];

        foreach ($roles as $value) {
            if (!$manager->getRepository(Role::class)->findByRoleName([$value])) {
                $role = new Role();
                $role->setRoleName($value);
                $role->setLibelle($value.'.libelle');
                $role->setDescription($value.'.description');
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
            $user->setInitMdp(false);
            $user->setValid(true);
            $user->setDeleted(false);
            $user->setAdmin(true);
            $manager->persist($user);
            $manager->flush();
        }
    }
}
