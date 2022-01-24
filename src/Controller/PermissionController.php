<?php


namespace App\Controller;



use App\Entity\Permission;
use App\Factory\ServiceFactory;
use App\Form\PermissionFormType;
use App\Repository\Interfaces\PermissionRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class PermissionController extends BaseController
{
    /**
     * @var PermissionRepositoryInterface
     */
    private $permissionRepository;

    public function __construct(ServiceFactory $serviceFactory, PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository=$permissionRepository;
        parent::__construct($serviceFactory);
    }

    /**
     * @Route("/admin/permission/index",name="app_admin_permissions_list")
     *
     *
     */
    public function list()
    {
        return $this->render("admin/permission/permission.html.twig");
    }
    /**
     * @Route("/admin/permission",name="app_admin_permissions")
     * @param Request $request
     * @return JsonResponse
     */
    public function permissions(Request $request)
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
            $length=$this->permissionRepository->count([]);
        }
        $permissions=$this->permissionRepository->search($filters, $start, $length);

        $output = array(
            'data' => [],
            'recordsFiltered' => $permissions->count(),
            'recordsTotal' => $this->permissionRepository->count([])
        );
        if($permissions->count()){
            foreach ($permissions as $permission){
                $action='<a class="btn btn-primary" href="'.$this->generateUrl('app_admin_edit_permission', ['id' => $permission->getId()]).'">
													<i class="fa fa-edit"></i>
												</a>
												<a href="'.$this->generateUrl('app_admin_delete_permission', ['id' => $permission->getId()]).'" class="btn btn-danger disable-btn del-link" type="submit">
													<i class="fa fa-trash"></i>
												</a>';


                $d = [
                    'select_item' => '<input type="checkbox" class="checkboxes" value="' . $permission->getId() . '" name="permissions_id[]"  />',
                    'id' => $permission->getId(),
                    'name' => $permission->getName(),
                    'actions' => $action,
                ];
                $output['data'][] = $d;
            }
        }
        if(!$format){
            return new JsonResponse($output);
        }

        return $this->getService('sf.permission')->export('permissions', $output['data'], $columns, $format);

    }
    /**
     * @Route("/admin/permission/new",name="app_admin_new_permission")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function newPermission(Request $request, TranslatorInterface $translator)
    {

        $form = $this->createForm(PermissionFormType::class, null, ["translator" => $translator]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var  Permission $permission */
            $permission = $form->getData();
            $data=[];
            $permission=$this->permissionRepository->createOrUpdate($permission, $data);
            if(!$permission || !$permission->getId()){
                $this->addFlash("error", $translator->trans('backend.permission.add_permission_error'));
                return $this->redirectToRoute("app_admin_permissions_list");
            }
            $this->addFlash("success", $translator->trans('backend.permission.add_permission'));
            return $this->redirectToRoute("app_admin_permissions_list");
        }
        return $this->render("admin/permission/permissionform.html.twig", ["permissionForm" => $form->createView()]);
    }

    /**
     * @Route("/admin/permission/edit/{id}",name="app_admin_edit_permission")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function editRole(Permission $permission, Request $request, TranslatorInterface $translator)
    {
        $form = $this->createForm(PermissionFormType::class, $permission, ["translator" => $translator]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->permissionRepository->createOrUpdate($permission);
            $this->addFlash("success", $translator->trans('backend.permission.modify_permission'));
            return $this->redirectToRoute("app_admin_permissions_list");
        }
        return $this->render("admin/role/permissionform.html.twig", ["permissionForm" => $form->createView()]);
    }
    /**
     * @Route("/admin/permission/delete/{id}",name="app_admin_delete_permission")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function delete(Permission $permission)
    {
        $this->permissionRepository->delete($permission);
        return $this->json(["message" => "success"]);
    }
    /**
     * @Route("/admin/role/groupaction",name="app_admin_groupaction_role")
     * @IsGranted("ROLE_SUPERUSER")
     */
    public function groupAction(Request $request, TranslatorInterface $translator)
    {
        $action = $request->get("action");
        $ids = $request->get("ids");
        $permissions = $this->permissionRepository->findBy(["id" => $ids]);

        if ($action == $translator->trans('backend.user.delete')) {
            foreach ($permissions as $permission) {
                $this->permissionRepository->delete($permission);
                $this->permissionRepository->save($permission);
            }
        } else {
            return $this->json(["message" => "error"]);
        }
        return $this->json(["message" => "success", "nb" => count($permissions)]);
    }

}
