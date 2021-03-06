<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller
{
   
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);

    }
    public function loginAction(Request $request)
    {
        $helpers = $this->get(Helpers::Class);
        //reucibir  json por Post
        $json = $request->get('json',null);
        //array para devolver por defecto
        $data = array(
            'status' =>'error1',
            'data' =>'Send json via Post ¡¡'
        );
        if($json != null)
        {
            $params = json_decode($json);
            $email =(isset($params->email)) ? $params->email:null;
            $password =(isset($params->password)) ? $params->password:null;
            $getHash =(isset($params->getHash)) ? $params->getHash:null;
            
            $emailConstranint = new Assert\Email();
            $emailConstranint->message = "This email is not valid";
            $validate_email = $this->get("validator")->validate($email,$emailConstranint);
            //cifrar la contraseña
            $pwd = hash('sha256',$password);
            if( $email != null && count($validate_email) == 0 && $password != null)
            { 

                $jwt_auth = $this->get(JwtAuth::Class);
                
                if($getHash == null){
                    $signup = $jwt_auth->signup($email,$pwd); 
                }
                else{
                    $signup = $jwt_auth->signup($email,$pwd,true); 
                }
                return $this->json($signup);
                
            }
            else
            {
                $data = array(
                      'status' =>'error2',
                       'data' =>'Password or email is wrong'
                
                            );
            }
        }else
        {

        }
        return $helpers->json($data);
    }
    public function pruebasAction(Request $request){
        $helper = $this->get(Helpers::Class);
        $jwt_auth = $this->get(JwtAuth::Class);
        $token = $request->get("authorization",null);
        if($token && $jwt_auth->checkToken($token)){
            $em = $this->GetDoctrine()->getManager();
            $userRepo = $em->getRepository('BackendBundle:User');
            $users = $userRepo->findAll();
            
           
            return $helper->json(array(
            'status' => 'success',
            'users' => $users
        ));
        } else{
            return $this->json(array(
            'status' => 'error',
            'users' => 'Login Failed'
        ));
        } 
        
    }
}
