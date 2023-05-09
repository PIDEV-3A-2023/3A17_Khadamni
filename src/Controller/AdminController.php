<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AdminController extends AbstractController
{



    #[Route('/admin/modify/{id}', name: 'app_admin_modify')]
    public function modify(Request $request, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $form = $this->createFormBuilder($user)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('numTel', TextType::class)
            ->add('adresse', TextType::class)
            ->add('age', IntegerType::class)

            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_gererusers');
        }

        return $this->render('admin/modify.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/user/delete/{idUser}', name: 'app_user_delete')]
    public function deleteUser(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_gererusers');
    }

    #[Route('/admin/users_list',name: 'app_admin_gererusers')]
    public function gererUsers(EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/usersBack.html.twig',[
            'users' => $users
        ]);

    }

}
