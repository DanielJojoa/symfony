<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\User;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class UserController extends Controller{
    public function newAction(Request $request){
        $helpers = $this->get(Helpers::Class);
        $json = $request->get('json',null);
        $params = json_decode($json);
       
        $data = array(
            'status' =>'error1',
            'code' => 400,
            'msg' => 'User not Created ¡¡',
            "json" =>$json
        );
        
        if($json != null){
            $createdAt =new \Datetime("now");
            $role = 'user';
            
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name)) ? $params->name : null;
            $surename = (isset($params->surename)) ? $params->surename : null;
            $password = (isset($params->password)) ? $params->password : null;
            //cifrar la contraseña

            
            
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This mail is not valid ¡¡";
            $validate_email = $this->get('validator')->validate($email,$emailConstraint);
            
            if($email != null && count($validate_email) == 0 && $password != null &&  $name != null && $surename != null ){
                $user = new User();
                $user->setCreatedAt($createdAt);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surename);
                if($password != null){
                    $pwd = hash('sha256',$password);
                    $user->setPassword($pwd);
                }
                
                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository('BackendBundle:User')->findBy(array("email"=> $email));

                if (count($isset_user) == 0){
                    $em->persist($user);
                    $em->flush();
                    $data = array(
            'status' =>'success',
            'code' => 200,
            'msg' => 'User Created ¡¡',
            'user' => $user
            
                            );
                }else{
                    $data = array(
            'status' =>'error1',
            'code' => 400,
            'msg' => 'User not Created, user duplicated ¡¡',
            "json" =>$json
        );
                }

                
            }


        }
        return $helpers->json($data);
    }
    public function editAction(Request $request){
        $helpers = $this->get(Helpers::Class);
        $jwt_auth = $this->get(JwtAuth::Class);

        $token = $request->get('authorization',null);
        $authCheck = $jwt_auth->checkToken($token);
        
        if($authCheck == true){
            //entity manager
            $em = $this->getDoctrine()->getManager();
            $identity = $jwt_auth->checkToken($token,true);

            $user = $em->getRepository('BackendBundle:User')->findOneBy(array('id'=> $identity->sub));

            $json = $request->get('json',null);
            $params = json_decode($json);
        
            $data = array(
                'status' =>'error1',
                'code' => 400,
                'msg' => 'User not Created ¡¡',
                "json" =>$json
            );

        if($json != null){
            $createdAt =new \Datetime("now");
            $role = 'user';
            
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name)) ? $params->name : null;
            $surename = (isset($params->surename)) ? $params->surename : null;
            $password = (isset($params->password)) ? $params->password : null;
            

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This mail is not valid ¡¡";
            $validate_email = $this->get('validator')->validate($email,$emailConstraint);
            
            if($email != null && count($validate_email) == 0  &&  $name != null && $surename != null ){
                
                $user->setCreatedAt($createdAt);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surename);

                if($password != null){
                    $pwd = hash('sha256',$password);
                    $user->setPassword($pwd);
                }

                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository('BackendBundle:User')->findBy(array("email"=> $email));

                if (count($isset_user) == 0 || $identity->email == $email){
                    $em->persist($user);
                    $em->flush();
                    $data = array(
            'status' =>'success',
            'code' => 200,
            'msg' => 'User Updated ¡¡',
            'user' => $user
            
                            );
                }else{
                    $data = array(
            'status' =>'error',
            'code' => 400,
            'msg' => 'User not Updated, user duplicated ¡¡',
            "json" =>$json
        );
                }

                
            }


        }
        }else{
             $data = array(
            'status' =>'error',
            'code' => 400,
            'msg' => 'Authorization no valid ¡¡'
        );
        }

       
        return $helpers->json($data);
    }
}
