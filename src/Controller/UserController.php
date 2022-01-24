<?php


namespace App\Controller;


use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Factory\ServiceFactory;
use App\Form\ChangePwsdFormType;
use App\Form\UserFormType;
use App\Repository\Interfaces\RoleRepositoryInterface;
use App\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends BaseController
{
    private $userRepository;
    private $passwordEncoder;

    private $entityManager;
    private $roleRepository;

    public function __construct(ServiceFactory $serviceFactory, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        parent::__construct($serviceFactory);
    }

    public function fakepswd(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $user = new User();
        $user->setValid(true)
            ->setDeleted(false)
            ->setEmail("mam@ddd.com")
            ->setNomComplet("nom comp")
            ->setUsername("mamless")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->passwordEncoder->encodePassword($user, $request->get("password")));
        // $user = $this->userRepository->saveUser($user);
        return $this->json(["id" => $user->getId(), "password" => $user->getPassword(), "decode" => $this->passwordEncoder->isPasswordValid($user, 1)]);
    }
    /**
     * @Route("/admin/user/index",name="app_admin_users_list")
     *
     *
     */
    public function list()
    {
        return $this->render("admin/user/user.html.twig");
    }
    /**
     * @Route("/admin/user",name="app_admin_users")
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request)
    {
        $format = $request->get('format', false);
        $columns = $request->get('allVisiblecolumns', []);

        $length = $request->get('length');
        $length = $length && ($length != -1) ? $length : 0;

        $start = $request->get('start');
        $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

        $search = $request->get('columns');
        $order = $request->get('order', false);

        $filters = [
            'query' => $search,
            'order' => $order
        ];
        if($format){
            $start=0;
            $length=$this->userRepository->count([]);
        }
        $users=$this->userRepository->search($filters, $start, $length);

        $output = array(
            'data' => [],
            'recordsFiltered' => $users->count(),
            'recordsTotal' => $this->userRepository->count([])
        );
        if($users->count()){
            foreach ($users as $user){
                if($user->isValid()){
                    $status='<a class="btn btn-success activate-link" href="'.$this->generateUrl('app_admin_changevalidite_user', ['id' => $user->getId()]).'">
														<i class="fa fa-check"></i>
													</a>';
                }else{
                    $status='<a class="btn btn-warning activate-link" href="'.$this->generateUrl('app_admin_changevalidite_user', ['id' => $user->getId()]).'">
														<i class="fa fa-times"></i>
													</a>';
                }
                $action='<a class="btn btn-primary" href="'.$this->generateUrl('app_admin_edit_user', ['id' => $user->getId()]).'">
													<i class="fa fa-edit"></i>
												</a>
												<a href="'.$this->generateUrl('app_admin_delete_user', ['id' => $user->getId()]).'" class="btn btn-danger disable-btn del-link" type="submit">
													<i class="fa fa-trash"></i>
												</a>';
                $d = [
                    'select_item' => '<input type="checkbox" class="checkboxes" value="' . $user->getId() . '" name="users_id[]"  />',
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'nomComplet' => $user->getNomComplet(),
                    'status' => !$format ? $status : (int)$user->isValid(),
                    'actions' => $action,
                ];
                $output['data'][] = $d;
            }
        }
        if(!$format){
            return new JsonResponse($output);
        }

        return $this->getService('sf.user')->export('users', $output['data'], $columns, $format);

    }

    /**
     * @Route("/admin/user/new",name="app_admin_new_user")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function newUser(Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(UserFormType::class, null, ["translator" => $translator]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var  User $user */
            $user = $form->getData();
            $password = $form["justpassword"]->getData();
            /** @var Role $role */
            $role = $form["role"]->getData();
            $user=$this->userRepository->createOrUpdate($user, $role, $this->passwordEncoder->encodePassword($user, $password));
            if(!$user || !$user->getId()){
                $this->addFlash("error", $translator->trans('backend.user.add_user_error'));
                return $this->redirectToRoute("app_admin_users_list");
            }
            $this->addFlash("success", $translator->trans('backend.user.add_user'));
            return $this->redirectToRoute("app_admin_users_list");
        }
        return $this->render("admin/user/userform.html.twig", ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/admin/user/edit/{id}",name="app_admin_edit_user")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function editUser(User $user, Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(UserFormType::class, $user, ["translator" => $translator]);
        $role = $this->roleRepository->findOneBy(["roleName" => $user->getRoles()[0]]);
        $form->get('role')->setData($role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Role $role */
            $role = $form["role"]->getData();
            $password = $form["justpassword"]->getData();
            $encodedPassword=$this->passwordEncoder->encodePassword($user, $password);
            if(empty($password)){
                $encodedPassword=$user->getPassword();
            }
            $this->userRepository->createOrUpdate($user, $role, $encodedPassword);
            $this->addFlash("success", $translator->trans('backend.user.modify_user'));
            return $this->redirectToRoute("app_admin_users_list");
        }
        return $this->render("admin/user/userform.html.twig", ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/admin/user/changeValidity/{id}",name="app_admin_changevalidite_user",methods={"post"})
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function activate(User $user)
    {
        $user = $this->userRepository->changeValidity($user);
        return $this->json(["message" => "success", "value" => $user->isValid()]);
    }

    /**
     * @Route("/admin/user/delete/{id}",name="app_admin_delete_user")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function delete(User $user)
    {
        $user = $this->userRepository->deleteSafe($user);
        /*$this->addFlash("success","Utilisateur supprimé");
        return $this->redirectToRoute('app_admin_users');*/
        return $this->json(["message" => "success", "value" => $user->isDeleted()]);
    }

    /**
     * @Route("/admin/user/changePassword",name="app_admin_changepswd")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function changePswd(Request $request, TranslatorInterface $translator)
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePwsdFormType::class, $user, ["translator" => $translator]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password =  $form["justpassword"]->getData();
            $newPassword = $form["newpassword"]->getData();

            if ($this->passwordEncoder->isPasswordValid($user, $password)) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
            } else {
                $this->addFlash("error", $translator->trans('backend.user.new_passwod_must_be'));
                return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
            }

            $this->userRepository->save($user);
            $this->addFlash("success", $translator->trans('backend.user.changed_password'));
            return $this->redirectToRoute("app_admin_index");
        }
        return $this->render("admin/params/changeMdpForm.html.twig", ["passwordForm" => $form->createView()]);
    }

    /**
     * @Route("/admin/user/groupaction",name="app_admin_groupaction_user")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function groupAction(Request $request, TranslatorInterface $translator)
    {
        $action = $request->get("action");
        $ids = $request->get("ids");
        $users = $this->userRepository->findBy(["id" => $ids]);

        if ($action == $translator->trans('backend.user.deactivate')) {
            foreach ($users as $user) {
                $user->setValid(false);
                $this->entityManager->persist($user);
            }
        } else if ($action == $translator->trans('backend.user.Activate')) {
            foreach ($users as $user) {
                $user->setValid(true);
                $this->entityManager->persist($user);
            }
        } else if ($action == $translator->trans('backend.user.delete')) {
            foreach ($users as $user) {
                $user->setDeleted(true);
                $this->entityManager->persist($user);
            }
        } else {
            return $this->json(["message" => "error"]);
        }
        $this->entityManager->flush();
        return $this->json(["message" => "success", "nb" => count($users)]);
    }

}
