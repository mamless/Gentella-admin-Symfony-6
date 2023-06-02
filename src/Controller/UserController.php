<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\ChangePwsdFormType;
use App\Form\UserFormType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends BaseController
{
    public function __construct(private UserRepository $userRepository, private TranslatorInterface $translator ,private RoleRepository $roleRepository, private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $entityManager)
    {
    }

    public function fakepswd(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = new User();
        $user->setValid(true)
            ->setDeleted(false)
            ->setEmail('mam@ddd.com')
            ->setNomComplet('nom comp')
            ->setUsername('mamless')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($user, $request->get('password')));
        // $user = $this->userRepository->saveUser($user);
        return $this->json(['id' => $user->getId(), 'password' => $user->getPassword(), 'decode' => $this->passwordHasher->isPasswordValid($user, 1)]);
    }

    #[Route(path: '/admin/user', name: 'app_admin_users')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function users(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('admin/user/user.html.twig', ['users' => $users]);
    }

    #[Route(path: '/admin/user/new', name: 'app_admin_new_user')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function newUser(Request $request)
    {
        $form = $this->createForm(UserFormType::class, null, ['translator' => $this->translator]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            /** @var Role $role */
            $password = $form['justpassword']->getData();
            $role = $form['role']->getData();
            $user->setValid(true)
                ->setDeleted(false)
                ->setAdmin(true)
                ->setPassword($this->passwordHasher->hashPassword($user, $password))
                ->setRoles([$role->getRoleName()]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('backend.user.add_user'));

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user/userform.html.twig', ['userForm' => $form]);
    }

    #[Route(path: '/admin/user/edit/{id}', name: 'app_admin_edit_user')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(UserFormType::class, $user, ['translator' => $this->translator]);
        $form->get('justpassword')->setData($user->getPassword());
        $therole = $this->roleRepository->findOneBy(['roleName' => $user->getRoles()[0]]);
        $form->get('role')->setData($therole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Role $role */
            $role = $form['role']->getData();
            $password = $form['justpassword']->getData();
            $user->setRoles([$role->getRoleName()]);
            if ($user->getPassword() != $password) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('backend.user.modify_user'));

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user/userform.html.twig', ['userForm' => $form]);
    }

    #[Route(path: '/admin/user/changevalidite/{id}', name: 'app_admin_changevalidite_user', methods: ['post'])]
    #[IsGranted('ROLE_SUPERUSER')]
    public function activate(User $user): JsonResponse
    {
        $user = $this->userRepository->changeValidite($user);

        return $this->json(['message' => 'success', 'value' => $user->isValid()]);
    }

    #[Route(path: '/admin/user/delete/{id}', name: 'app_admin_delete_user')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function delete(User $user): JsonResponse
    {
        $user = $this->userRepository->delete($user);
        /*$this->addFlash("success","Utilisateur supprimé");
        return $this->redirectToRoute('app_admin_users');*/
        return $this->json(['message' => 'success', 'value' => $user->isDeleted()]);
    }

    #[Route(path: '/admin/user/changePassword', name: 'app_admin_changepswd')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function changePswd(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePwsdFormType::class, $user, ['translator' => $this->translator]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form['justpassword']->getData();
            $newPassword = $form['newpassword']->getData();

            if ($this->passwordHasher->isPasswordValid($user, $password)) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            } else {
                $this->addFlash('error', 'backend.user.new_passwod_must_be');

                return $this->render('admin/params/changeMdpForm.html.twig', ['passwordForm' => $form]);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('backend.user.changed_password'));

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/params/changeMdpForm.html.twig', ['passwordForm' => $form]);
    }

    #[Route(path: '/admin/user/groupaction', name: 'app_admin_groupaction_user')]
    #[IsGranted('ROLE_SUPERUSER')]
    public function groupAction(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $ids = $request->get('ids');
        $users = $this->userRepository->findBy(['id' => $ids]);

        if ($action == $this->translator->trans('backend.user.deactivate')) {
            foreach ($users as $user) {
                $user->setValid(false);
                $this->entityManager->persist($user);
            }
        } elseif ($action == $this->translator->trans('backend.user.Activate')) {
            foreach ($users as $user) {
                $user->setValid(true);
                $this->entityManager->persist($user);
            }
        } elseif ($action == $this->translator->trans('backend.user.delete')) {
            foreach ($users as $user) {
                $user->setDeleted(true);
                $this->entityManager->persist($user);
            }
        } else {
            return $this->json(['message' => 'error']);
        }
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'nb' => count($users)]);
    }
}
