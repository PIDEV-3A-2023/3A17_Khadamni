<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[Route('/user')]
class UserController extends AbstractController {


#[Route('/', name: 'app_user_index', methods: ['GET'])]
public function index(EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $userEntity = $entityManager
        ->getRepository(User::class)
        ->findOneBy(['idUser' => $user->getIdUser()]);

    return $this->render('user/index.html.twig', [
        'user' => $userEntity,
    ]);
}

    #[Route('/search',name: 'app_user_searchusers',methods:['GET'])]
    public function searchUsers(Request $req,NormalizerInterface $normalizer,EntityManagerInterface $entityManager)
    {

        $searchValue = $req->get('searchValue');
        $users = $entityManager->getRepository(User::class)->findUsersByName($searchValue);
        $jsonContent = $normalizer->normalize($users,'json');
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    #[Route('/{idUser}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{idUser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }






}
