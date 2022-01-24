<?php

namespace App\Command;

use App\Entity\Permission;
use App\Entity\User;
use App\Tools\Query;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GeneratePermissions extends Command
{
    use LockableTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('permissions:generate')
            // the short description shown while running "php bin/console list"
            ->setDescription('Generate new permissions.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you generate new permissions. ...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }
        $output->writeln(['START', '============', '']);

        $queryHelper=$this->container->get('sf.query');
        $em=$this->container->get('doctrine.orm.entity_manager');
        $roleService=$this->container->get('sf.role');
        $permissionService=$this->container->get('sf.permission');
        $metas=$em->getMetadataFactory()->getAllMetadata();
        $actions=['index', 'edit', 'delete'];

        foreach ($metas as $meta) {
            $entity = strtolower(str_replace("App\Entity".DIRECTORY_SEPARATOR,'', $meta->getName()));
            foreach ($actions as $action){
               $data=[
                   'name'=>$entity.'.'.$action
               ];
                $queryHelper->insert('permission', $data, $queryHelper::INSERT_IGNORE);
            }
        }

        $supperUserRole=$roleService->getRepository()->findOneBy(['roleName'=>'ROLE_SUPERUSER']);
        $permissions=$permissionService->getRepository()->findAll();
        if(!empty($permissions)){
            foreach ($permissions as $permission){
                $permission->addRole($supperUserRole);
                $permissionService->getRepository()->save($permission);
            }
        }
        $output->writeln(['END', '============', '']);
        $this->release();
        return Command::SUCCESS;
    }
}
