<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;
class JwtAuth
{ 
    public $manager;
    public $key;
    public function __construct($manager){
        $this->manager = $manager;
        $this->key = "holaquetalsoylaclavesecreta12345";
    } 
    public function signup($email,$password, $getHash = null)
    {   $user = $this->manager->getRepository('BackendBundle:User')->findOneBy(array(
        "email" => $email,
        "password" =>$password
    ));
    $signup  = false;
        if(is_object($user))
        {
            $signup = true;
        }
        if ($signup == true)
        {   
            $token = array(
                "sub" => $user->getId(),
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "surname" => $user->getSurname(),
                "iat" => time(),
                "exp" => time()+(7*24*60*60)

            );
            $jwt = JWT::encode($token,$this->key,'HS256');
            $deconded = JWT::decode($jwt,$this->key,array('HS256'));
            if($getHash ==null){
                $data = $jwt;
            }
            else{
                $data = $deconded;
            }
            
            return $data;
        }else{
            $data = array (
                "status"=> "error",
                "user" => "Login Failed"
        
            );
        }
        return $data;
    }
}