<?php

namespace App\Controller;
use App\Entity\Emploi;
use App\Entity\Reclamation;
use App\Entity\Stage;
use App\Entity\SuiviReclamation;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Formation;
use App\Entity\User;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class AdminController extends AbstractController
{



    #[Route('/admin/modify/{id}', name: 'app_admin_modify')]
    public function modify(Request $request, $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $selectedOption = $request->request->get('my_select');

        $role = $entityManager->getRepository(Role::class)->findOneBy(['idRole' => $selectedOption]);

       $user->setIdRole($role);
       $entityManager->flush();
       return $this->redirectToRoute("app_admin_gererusers",[]);

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
    #[Route('/admin/formation',name: 'app_admin_gererformation')]
    public function gererFormation(EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $formations = $em->getRepository(Formation::class)->findAll();
        return $this->render('admin/FormationBack.html.twig',[
            'formations' => $formations
        ]);

    }
    #[Route('/admin/stages',name: 'app_admin_stagesback')]
    public function stagesBack(EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $stages = $em->getRepository(Stage::class)->findAll();
        return $this->render('admin/StageBack.twig',[
            'stages' => $stages
        ]);

    }
    #[Route('/admin/users_list', name: 'app_admin_gererusers')]
    public function gererUsers(EntityManagerInterface $entityManager, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $entityManager->getRepository(User::class)->findAll();
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('idRole', ChoiceType::class, [
                'choices' => [
                    'Admin' => 1,
                    'User' => 2,
                    'Recruteur' => 3
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Role'
            ])
            ->add('Modifier', SubmitType::class)
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIdUser($form->get("idRole")->getData());
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_gererusers');
        }


        return $this->render('admin/usersBack.html.twig', [
            'form' => $form->createView(), // pass the form view instead of the form itself
            'users' => $users
        ]);
    }



    #[Route('/admin/dash',name: 'app_admin_maindashboard')]
    public function mainDashboard(EntityManagerInterface $entityManager, ChartBuilderInterface $chartBuilder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $entityManager
            ->getRepository(User::class)
            ->findAll();
        $totalUser = count($user);

        $formation = $entityManager
            ->getRepository(Formation::class)
            ->findAll();
        $totalformation = count($formation);

        $emploi = $entityManager
            ->getRepository(Emploi::class)
            ->findAll();
        $totalemploi = count($emploi);

        $chartData = $this->stat($chartBuilder, $entityManager);

        return $this->render('admin/dashboard.html.twig', [
            'user' => $totalUser,
            'formation' => $totalformation,
            'emploi' => $totalemploi,
            'chart' => $chartData[0],
            'chart2' => $chartData[1],
            'chart3' => $chartData[2],
        ]);
    }


    public function stat(ChartBuilderInterface $chartBuilder, EntityManagerInterface $entityManager)
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart2 = $chartBuilder->createChart(Chart::TYPE_LINE);

        $x = 0;
        $y = 0;
        $z = 0;

        $suiviReclamation = $entityManager
            ->getRepository(SuiviReclamation::class)
            ->findAll();

        for ($i = 0; $i < count($suiviReclamation); $i++) {
            if ($suiviReclamation[$i]->getEtatReclamation() == "en_cours") {
                $x += 1;
            } elseif ($suiviReclamation[$i]->getEtatReclamation() == "non traitee") {
                $y += 1;
            } else {
                $z += 1;
            }
        }

        $chart->setData([
            'labels' => ['En Cours', 'non Traité', 'Traité'],
            'datasets' => [
                [
                    'label' => 'Réclamations',
                    'backgroundColor' => ['#007bff', '#28a745', '#dc3545'],
                    'borderColor' => ['#007bff', '#28a745', '#dc3545'],
                    'data' => [$x, $y, $z],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 10,
                ],
            ],
        ]);

        $reclamations = $entityManager->getRepository(Reclamation::class)->findAll();

        // Initialize counts for each year to zero
        $reclamations2021 = 0;
        $reclamations2022 = 0;
        $reclamations2023 = 0;

        // Loop through all reclamations and count the ones for each year
        foreach ($reclamations as $reclamation) {
            $year = $reclamation->getDateReclamation()->format('Y');

            if ($year == '2021') {
                $reclamations2021++;
            } elseif ($year == '2022') {
                $reclamations2022++;
            } elseif ($year == '2023') {
                $reclamations2023++;
            }
        }

        // Set the chart data using the counts for each year
        $chart2->setData([
            'labels' => ['2021', '2022', '2023'],
            'datasets' => [
                [
                    'label' => 'Réclamations',
                    'backgroundColor' => ['#007bff', '#28a745', '#dc3545'],
                    'borderColor' => ['#007bff', '#28a745', '#dc3545'],
                    'data' => [$reclamations2021, $reclamations2022, $reclamations2023],
                ],
            ],
        ]);

        $chart2->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 10,
                ],
            ],
        ]);
        $chart3 = $chartBuilder->createChart(Chart::TYPE_PIE);


        $x3=0;
        $y3=0;
        $z3=0;


        $user = $entityManager
            ->getRepository(user::class)
            ->findAll();


        for($i=0;$i<count($user);$i++){

            if($user[$i]->getAge() < 20){
                $x3+=1;
            }else
                if($user[$i]->getAge() >= 20){
                    $y3+=1;
                }else {$z3+=1;}
        }


        $chart3->setData([
            'labels' => ['age<20', 'entre 20 et 30 ans', 'supérieure a 30 ans '],
            'datasets' => [
                [
                    'label' => 'age',
                    'backgroundColor' => ['#007bff', '#28a745', '#dc3545'],
                    'borderColor' => ['#007bff', '#28a745', '#dc3545'],
                    'data' => [$x3,$y3,$z3],
                ],
            ],
        ]);

        $chart3->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 50,
                ],
            ],
        ]);

        return [$chart, $chart2,$chart3];
    }


}

    
