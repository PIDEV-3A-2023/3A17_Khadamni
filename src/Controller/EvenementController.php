<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/evenement')]
class EvenementController extends AbstractController
{
    #[Route('/back', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();

        $totalResults = count($evenements);

        return $this->render('admin/EvenementsBack.twig', [
            'evenements' => $evenements,
            'totalResults' => $totalResults,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the uploads directory
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }

                $evenement->setImageFilename($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }


    




    #[Route('/back/{idevenement}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
            
        ]);
    }

    #[Route('/{idevenement}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/back/{idevenement}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getIdevenement(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}

/*
In the code above, we're checking if an image file was uploaded, and if so, we're generating a unique filename and moving the uploaded file to the uploads_directory directory. We're also setting the imageFilename property of the Evenement entity to the new filename.

Note that you'll need to configure the uploads_directory parameter in your services.yaml file to point to the directory where you want to store uploaded files. For example:

yaml
Copy code
parameters:
    uploads_directory: '%kernel.project_dir%/public/uploads'
With these modifications, your form should now include a





Mayla Bouzakoura
ou mettre le code EvenementType dans quel path
Vous pouvez mettre le code de la classe EvenementType dans le répertoire src/Form de votre projet Symfony. Si le dossier Form n'existe pas, vous pouvez le créer.

Une fois que vous avez créé le fichier EvenementType.php dans le répertoire src/Form, vous devez vous assurer que la classe est correctement définie avec le namespace suivant :

php
Copy code
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementType extends AbstractType
{
    // ...
}
Il est important de noter que le namespace doit correspondre au chemin du fichier EvenementType.php. Dans cet exemple, le chemin complet du fichier sera src/Form/EvenementType.php.
*/
