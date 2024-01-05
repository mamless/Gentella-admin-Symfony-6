<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileFormType;
use App\Repository\ProfileRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends BaseController
{
    public function __construct(public readonly ProfileRepository $profileRepository,private readonly EntityManagerInterface $entityManager,public readonly RoleRepository $roleRepository)
    {
    }

    #[Route(path: '/admin/profile', name: 'app_admin_profiles')]
    #[IsGranted('ROLE_LIST_PROFILE')]
    public function profiles(): Response
    {
        $profiles = $this->profileRepository->findBy(["deleted"=>false]);

        return $this->render('admin/profile/profile.html.twig', ['profiles' => $profiles]);
    }

    #[Route(path: '/admin/profile/new', name: 'app_admin_new_profile')]
    #[IsGranted('ROLE_ADD_PROFILE')]
    public function newProfile(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(ProfileFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $idsRoles = $request->get("chkgrp");
            $roles = $this->roleRepository->findBy(["id"=>$idsRoles]);
            if (sizeof($roles)==sizeof($idsRoles) && sizeof($idsRoles)>0 ){
                /** @var Profile $profile */
                $profile = $form->getData();
                $profile->setValid(true)
                    ->setDeleted(false)
                    ->setCreatedBy($this->getUser())
                    ->addRoles($roles);
                $this->entityManager->persist($profile);
                $this->entityManager->flush();
                $this->addFlash('success', 'Profile créé');
                return $this->redirectToRoute("app_admin_profiles");
            }else{
                $this->addFlash('error', "Une erreur s'est produite actualiser et reessayer ");
            }
        }
        $allRoles = $this->roleRepository->findAll();


        return $this->render('admin/profile/profileform.html.twig', ['roles' => $allRoles,"ProfileForm"=>$form->createView()]);
    }

    #[Route(path: '/admin/profile/edit/{id}', name: 'app_admin_edit_profile')]
    #[IsGranted('ROLE_EDIT_PROFILE')]
    public function editProfile(Profile $profile, Request $request): RedirectResponse|Response
    {
        $this->errorNotFound($profile);
        $form = $this->createForm(ProfileFormType::class,$profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $idsRoles = $request->get("chkgrp");
            $roles = $this->roleRepository->findBy(["id"=>$idsRoles]);

            if (sizeof($roles)==sizeof($idsRoles) && sizeof($idsRoles)>0 ){
                /** @var Profile $profile */
                $profile = $form->getData();
                $profile->setValid(true)
                    ->setDeleted(false)
                    ->setModfiedBy($this->getUser());
                foreach ($profile->getRoles() as $role) {
                    if (!in_array($role, $roles)){
                        $profile->removeRole($role);
                    }
                }
                foreach ($roles as $key => $role ) {
                    if (in_array($role,$profile->getRoles()->toArray())){
                        unset($roles[$key]);
                    }
                }

                $profile->addRoles($roles);
                $this->entityManager->persist($profile);
                $this->entityManager->flush();
                $this->addFlash('success', 'Profile modifié');
                return $this->redirectToRoute("app_admin_profiles");
            }else{
                $this->addFlash('error', "Une erreur s'est produite actualiser et reessayer ");
            }
        }
        $allRoles = $this->roleRepository->findAll();

        foreach ($allRoles as $key => $role ) {
            if (in_array($role,$profile->getRoles()->toArray())){
                unset($allRoles[$key]);
            }
        }
        return $this->render('admin/profile/profileform.html.twig', ['roles' => $allRoles,"ProfileForm"=>$form->createView()]);
    }

    #[Route(path: '/admin/profile/groupaction', name: 'app_admin_groupaction_profile')]
    #[IsGranted('ROLE_AG_PROFILE')]
    public function groupAction(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $ids = $request->get('ids');
        $profiles = $this->profileRepository->findBy(['id' => $ids]);
        if ($action == 'desactiver' && $this->isGranted('ROLE_AG_ENABLE_PROFILE')) {
            foreach ($profiles as $profile) {
                $this->errorNotFound($profile);
                $profile->setValid(false)->setModfiedBy($this->getUser());
                $this->entityManager->persist($profile);
            }
        } elseif ($action == 'activer' && $this->isGranted('ROLE_AG_ENABLE_PROFILE')) {
            foreach ($profiles as $profile) {
                $this->errorNotFound($profile);
                $profile->setValid(true)->setModfiedBy($this->getUser());
                $this->entityManager->persist($profile);
            }
        } elseif ($action == 'supprimer' && $this->isGranted('ROLE_AG_DELETE_PROFILE')) {
            foreach ($profiles as $profile) {
                $this->errorNotFound($profile);
                $profile->setModfiedBy($this->getUser());
                $this->profileRepository->delete($profile);
            }
        } else {
            return $this->json(['message' => 'error']);
        }
        $this->entityManager->flush();

        return $this->json(['message' => 'success', 'nb' => is_countable($profiles) ? count($profiles) : 0]);
    }

    #[Route(path: '/admin/profile/changevalidite/{id}', name: 'app_admin_changevalidite_profile', methods: ['post'])]
    #[IsGranted('ROLE_ENABLE_PROFILE')]
    public function activate(Profile $profile): JsonResponse
    {
        $this->errorNotFound($profile);
        $profile->setModfiedBy($this->getUser());
        $profile = $this->profileRepository->changeValidite($profile);

        return $this->json(['message' => 'success', 'value' => $profile->getValid()]);
    }

    #[Route(path: '/admin/profile/delete/{id}', name: 'app_admin_delete_profile')]
    #[IsGranted('ROLE_DELETE_PROFILE')]
    public function delete(Profile $profile): JsonResponse
    {
        $this->errorNotFound($profile);
        $profile->setModfiedBy($this->getUser());
        $profile = $this->profileRepository->delete($profile);

        return $this->json(['message' => 'success', 'value' => $profile->getDeleted()]);
    }

    public function errorNotFound(Profile $profile)
    {
        if ( $profile->isDeleted()){
            throw $this->createNotFoundException("User not found");
        }
    }
}