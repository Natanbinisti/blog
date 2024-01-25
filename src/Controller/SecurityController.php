<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Core\Controller\Controller;
use Core\Http\Response;
use Core\Session\Session;

class SecurityController extends Controller
{

    public function register():Response
    {

        $username = null;
        $unencryptedPassword = null;
        if(!empty($_POST['username'])) {
            $username = $_POST['username'];
        }
        if(!empty($_POST['password'])) {
            $unencryptedPassword = $_POST['password'];
        }

        if($username && $unencryptedPassword)
        {
            $userRepository = new UserRepository();

            $userExistant = $userRepository->findByUsername($username);

            if($userExistant){

                $this->addFlash("username déja utilisé","warning");
                return $this->redirect("?type=security&action=register");
            }

            $user = new User();
            $user->setUsername($username);
            $user->setPassword($unencryptedPassword);

            $userRepository->save($user);

            $this->addFlash("success" ,"success");
            return $this->redirect("?type=article&action=index");


        }


        return $this->render("user/register", [
            "pageTitle"=> "New profile"
        ]);
    }

    public function signIn():Response
    {
        $username = null;
        $unencryptedPassword = null;
        if(!empty($_POST['username'])) {
            $username = $_POST['username'];
        }
        if(!empty($_POST['password'])) {
            $unencryptedPassword = $_POST['password'];
        }

        if($username && $unencryptedPassword) {
            $userRepository = new UserRepository();

            $user = $userRepository->findByUsername($username);

            if (!$user) {

                $this->addFlash("wrong username", "danger");
                return $this->redirect("?type=security&action=signIn");
            }

            if(!$user->logIn($unencryptedPassword))
            {
                $this->addFlash("wrog password, ".$user->getUsername(), "danger");
                return $this->redirect("?type=security&action=signIn");
            }


            $this->addFlash("Coucou ".$user->getUsername() ,"success");
            return $this->redirect("?type=article&action=index");

        }

        return $this->render("user/signin", [
            "pageTitle"=> "Connexion"
        ]);
    }


    public function signOut():Response
    {
        Session::remove("user");
        $this->addFlash("bien déconnecté, tcha-tchao", "secondary");
        return $this->redirect("?type=article&action=index");
    }


}