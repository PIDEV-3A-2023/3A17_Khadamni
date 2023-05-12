<?php

namespace App\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Formation;
use App\Entity\User;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
class UserserviceController extends AbstractController
{

    
   
    #[Route('/registerjson/new', name: 'app_registerjson')]
    public function registerjson(Request $request,EntityManagerInterface $em, NormalizerInterface $Normalizer, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
    
        $userRole = $em->getRepository(Role::class)->find(2);
        $user->setIdRole($userRole);
        $user->setEtatUser(0);
        $cryptedpassword = $userPasswordHasher->hashPassword($user , $request->get('mdp'));
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));
        $user->setNumTel($request->get('numTel'));
        $user->setEmail($request->get('email'));
        $user->setMdp($cryptedpassword);
        $user->setAdresse($request->get('adresse'));
        $user->setAge($request->get('age'));

         
            $em->persist($user);
            $em->flush();


        // Return JSON response
            $jsonContent = $Normalizer->normalize($user,'json');
            return new Response(json_encode($jsonContent));
        }



        

      
        #[Route('/loginjson', name: 'app_loginjson')]
        public function signinAction(Request $request,UserPasswordHasherInterface $passwordHasher  ,EntityManagerInterface $entityManager,SerializerInterface $serializer   ) {
            $email = $request->get('email');
            $mdp = $request->get('mdp');
           
            $user = $entityManager->getRepository(User::class)->findOneBy(['email'=>$email]);
            if (isset($user)) {
                $isValid = $passwordHasher->isPasswordValid($user,$mdp);
                if ($isValid) {
                   
                    $userJSON = $serializer->serialize($user,'json');
                    return new Response($userJSON);
                }
                else return new Response('incorrect password');
    
            }
            else return new Response('user not found');
            }
          
            
            #[Route('/editjson/{idUser}', name: 'app_user_api_editjson', methods: ['GET', 'POST'])]
            public function edit(Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer,$idUser,UserPasswordHasherInterface $userPasswordHasher): Response
            {
        
                    $user = $entityManager->getRepository(User::class)->find($idUser);
                    $nom = $request->get('nom');
                    $prenom = $request->get('prenom');
                    
                    $mdp= $request->get('mdp');
                   
                    if (isset($mdp) and strlen($mdp) > 5 ) {
                        $cryptedpassword = $userPasswordHasher->hashPassword($user , $request->get('mdp'));
                        
                        $user->setMdp($cryptedpassword);
                    }
                    
        
        
                    if(isset($nom))
                    $user->setNom($nom);
                    if(isset($prenom))
                    $user->setPrenom($prenom);
                   
        
                    $entityManager->flush();
        
                    $userjson = $serializer->serialize($user,'json');
        
                    return new Response($userjson);
        
            }
            
        }
             
            
             
            
            
       
    