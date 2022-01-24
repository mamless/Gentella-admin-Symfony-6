<?php


namespace App\Controller;



use App\Entity\Role;
use App\Entity\User;
use App\Factory\ServiceFactory;
use App\Form\RoleFormType;
use App\Form\UserFormType;
use App\Repository\Interfaces\PermissionRepositoryInterface;
use App\Repository\Interfaces\RoleRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class RoleController extends BaseController
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;
    /**
     * @var PermissionRepositoryInterface
     */
    private $permissionRepository;

    public function __construct(ServiceFactory $serviceFactory, RoleRepositoryInterface $roleRepository, PermissionRepositoryInterface $permissionRepository)
    {
        $this->roleRepository=$roleRepository;
        $this->permissionRepository=$permissionRepository;
        parent::__construct($serviceFactory);
    }

    /**
     * @Route("/admin/role/index",name="app_admin_roles_list")
     *
     *
     */
    public function list()
    {
        return $this->render("admin/role/role.html.twig");
    }
    /**
     * @Route("/admin/role",name="app_admin_roles")
     * @param Request $request
     * @return JsonResponse
     */
    public function roles(Request $request)
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
            $length=$this->roleRepository->count([]);
        }
        $roles=$this->roleRepository->search($filters, $start, $length);

        $output = array(
            'data' => [],
            'recordsFiltered' => $roles->count(),
            'recordsTotal' => $this->roleRepository->count([])
        );
        if($roles->count()){
            foreach ($roles as $role){
                $action='<a class="btn btn-primary" href="'.$this->generateUrl('app_admin_edit_role', ['id' => $role->getId()]).'">
													<i class="fa fa-edit"></i>
												</a>
												<a href="'.$this->generateUrl('app_admin_delete_role', ['id' => $role->getId()]).'" class="btn btn-danger disable-btn del-link" type="submit">
													<i class="fa fa-trash"></i>
												</a>';
                $permissions=$role->getPermissions();
                $permissions = $permissions->map(function($obj){return $obj->getName();})->getValues();

                $d = [
                    'select_item' => '<input type="checkbox" class="checkboxes" value="' . $role->getId() . '" name="roles_id[]"  />',
                    'id' => $role->getId(),
                    'roleName' => $role->getRoleName(),
                    'permissions' => implode(",\n", $permissions),
                    'actions' => $action,
                ];
                $output['data'][] = $d;
            }
        }

        if(!$format){
            return new JsonResponse($output);
        }

        return $this->getService('sf.role')->export('roles', $output['data'], $columns, $format);

    }
    /**
     * @Route("/admin/role/new",name="app_admin_new_role")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function newRole(Request $request, TranslatorInterface $translator)
    {

        $form = $this->createForm(RoleFormType::class, null, ["translator" => $translator]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var  Role $user */
            $role = $form->getData();
            /** @var ArrayCollection $permissions */
            $permissions=$form["permission"]->getData();

            $data['permissions']=$permissions;
            $role=$this->roleRepository->createOrUpdate($role, $data);
            if(!$role || !$role->getId()){
                $this->addFlash("error", $translator->trans('backend.role.add_role_error'));
                return $this->redirectToRoute("app_admin_roles_list");
            }
            $this->addFlash("success", $translator->trans('backend.role.add_role'));
            return $this->redirectToRoute("app_admin_roles_list");
        }
        return $this->render("admin/role/roleform.html.twig", ["roleForm" => $form->createView()]);
    }

    /**
     * @Route("/admin/role/edit/{id}",name="app_admin_edit_role")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function editRole(Role $role, Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(RoleFormType::class, $role, ["translator" => $translator]);
        $form->get('permission')->setData($role->getPermissions()->toArray());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ArrayCollection $permissions */
            $permissions=$form["permission"]->getData();
            $data['permissions']=$permissions;
            $this->roleRepository->createOrUpdate($role, $data);
            $this->addFlash("success", $translator->trans('backend.role.modify_role'));
            return $this->redirectToRoute("app_admin_roles_list");
        }
        return $this->render("admin/role/roleform.html.twig", ["roleForm" => $form->createView()]);
    }
    /**
     * @Route("/admin/role/delete/{id}",name="app_admin_delete_role")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function delete(Role $role)
    {
        $role = $this->roleRepository->deleteSafe($role);
        return $this->json(["message" => "success", "value" => $role->isDeleted()]);
    }
    /**
     * @Route("/admin/role/groupaction",name="app_admin_groupaction_role")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function groupAction(Request $request, TranslatorInterface $translator)
    {
        $action = $request->get("action");
        $ids = $request->get("ids");
        $roles = $this->roleRepository->findBy(["id" => $ids]);

        if ($action == $translator->trans('backend.user.delete')) {
            foreach ($roles as $role) {
                $role->setDeleted(true);
                $this->roleRepository->save($role);
            }
        } else {
            return $this->json(["message" => "error"]);
        }
        return $this->json(["message" => "success", "nb" => count($roles)]);
    }

}
