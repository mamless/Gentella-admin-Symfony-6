<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\ServiceFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    /**
     * @var ServiceFactory
     */
    protected $serviceFactory;

    public function __construct(ServiceFactory $serviceFactory)
    {
        $this->serviceFactory=$serviceFactory;
    }

    /**
     * @param $alias
     * @return \Exception|object|null
     */
    public function getService($alias){
        if($this->serviceFactory->hasService($alias)){
            return $this->serviceFactory->getService($alias);
        }
        return new \Exception('Service '.$alias.' not found');
    }
    protected function getUser(): User
    {
        return parent::getUser();
    }

    protected function getId()
    {
        return $this->getUser()->getId();
    }
}
