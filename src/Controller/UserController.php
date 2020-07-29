<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $userRepository;
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/user/new",name="app_new_user")
     * @IsGranted("ROLE_USER")
     */
    public function newUser(Request $request){
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $user = new User();
        $user->setValid(true)
            ->setDeleted(false)
            ->setEmail("mam@ddd.com")
            ->setNomComplet("nom comp")
            ->setUsername("mamless")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->passwordEncoder->encodePassword($user,$request->get("password")));
       // $user = $this->userRepository->saveUser($user);
        return $this->json(["id"=>$user->getId(),"password"=>$user->getPassword(),"decode"=>$this->passwordEncoder->isPasswordValid($user,1)]);
    }
}
