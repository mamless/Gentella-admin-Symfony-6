<?php

namespace App\DataFixtures;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->encoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $roles = [
            "ROLE_SUPERUSER" => "Super Admin",
            "ROLE_EDITORIAL" => "Manager",
            "ROLE_ADMINISTRATOR" => "Admin",
            "ROLE_WRITER" => "Redacteur"
        ];

        foreach ($roles as $key => $value) {
            if (!$manager->getRepository(Role::class)->findOneBy(['roleName'=>$key])) {
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
            $user->setRoles(["ROLE_SUPERUSER"]);
            $user->setPassword($this->encoder->encodePassword($user, 'admin'));
            $user->setNomComplet('Admin');
            $user->setEmail('admin@example.com');
            $user->setValid(true);
            $user->setDeleted(false);
            $user->setAdmin(true);
            $manager->persist($user);


        }
        $metas=$manager->getMetadataFactory()->getAllMetadata();
        $actions=['index', 'edit', 'delete'];

        foreach ($metas as $meta) {
            $entity = strtolower(str_replace("App\Entity".DIRECTORY_SEPARATOR,'', $meta->getName()));
            foreach ($actions as $action){
                $permission= new Permission();
                $permission->setName($entity.'.'.$action);

                $manager->persist($permission);
                $manager->flush();
                dump($permission->getId());
                exit;
                $user=$manager->getRepository(User::class)->findOneBy(['email'=>'admin@example.com']);
                $user->addPermission($permission);
                $manager->persist($user);
                $manager->flush();
            }
        }

        dump($actions);
        exit;

    }
}
