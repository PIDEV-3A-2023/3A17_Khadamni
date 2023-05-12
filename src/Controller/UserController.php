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
    #[Route('/quiz', name: 'app_user_userquiz', methods: ['GET', 'POST'])]
    public function userQuiz(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $quiz = [
            [
                'question' => 'Quelle syntaxe est correcte pour que la fonction init soit appelée au chargement de la page ?',
                'answers' => [
                    'window.onload = init;',
                    'window.onload = init();',
                    'window.onload() = init;',
                    'window.onload() = init();'
                ],
                'correct_answer' => 0
            ],
            [
                'question' => 'Quelle valeur de display n’existe pas ?',
                'answers' => [
                    'inline-table',
                    'inline-flex',
                    'inline-grid',
                    'inline-float'

                ],
                'correct_answer' => 3
            ],
            [
                'question' => 'Quelle est la valeur par défaut de la propriété position ?',
                'answers' => [
                    'relative',
                    'none',
                    'auto',
                    'inherit',
                    'static'
                ],
                'correct_answer' => 4
            ],
            [
                'question' => 'Par défaut, dans quel ordre de priorité sont affectées les variables envoyées par HTTP ?',
                'answers' => [
                    'get, cookie, post',
                    'post, get, cookie',
                    'get, post, cookie',
                    'post,cookie, get'
                ],
                'correct_answer' => 2
            ],
            [
                'question' => 'Quelle fonction de bufferisation (temporisation) envoie au navigateur les données contenues par le tampon, et stoppe la tamporisation de sortie ?',
                'answers' => [
                    'ob_flush()',
                    'ob_end_flush()',
                    'ob_get_flush()'
                ],
                'correct_answer' => 1
            ],
            [
                'question' => 'Quelle méthode permet d\'être sûr d\'exécuter une instruction lorsque le DOM a été bien mis à jour ?',
                'answers' => [
                    'Vue.sync',
                    'Vue.nextTick',
                    'Vue.refresh',
                    'Vue.await'
                ],
                'correct_answer' => 1
            ],
            [
                'question' => 'Quelle variable spéciale contient l\'événement natif attrapé par v-on ?',
                'answers' => [
                    '$event',
                    'nativeEvent',
                    'e',
                    'e.native'
                ],
                'correct_answer' => 0
            ],
            [
                'question' => 'Quelle fonction retourne la longueur d\'une chaîne de texte ?',
                'answers' => [
                    'strlen',
                    'strlength',
                    'length',
                    'substr'
                ],
                'correct_answer' => 0
            ],
            [
                'question' => 'Quelle fonction permet d\'envoyer des en-têtes HTTP au navigateur avant le contenu de la page ?',
                'answers' => [
                    'parse_url()',
                    'http_post()',
                    'header()'
                ],
                'correct_answer' => 2
            ],
            [
                'question' => 'Quelle fonction permet d\'effacer un fichier ?',
                'answers' => [
                    'delete()',
                    'unlink()',
                    'remove()',
                    'clearfile()'
                ],
                'correct_answer' => 1
            ],


        ];


        return $this->render('user/quizz.html.twig',[
            'quiz' => $quiz,

        ]);

    }
    #[Route('/quizSubmit', name: 'app_user_quizsubmit', methods: ['GET', 'POST'])]
    public function QuizSubmit(Request $request,  EntityManagerInterface $entityManager): Response
    {

        $quiz = [
            [
                'question' => 'Quelle syntaxe est correcte pour que la fonction init soit appelée au chargement de la page ?',
                'answers' => [
                    'window.onload = init;',
                    'window.onload = init();',
                    'window.onload() = init;',
                    'window.onload() = init();'
                ],
                'correct_answer' => 0
            ],
            [
                'question' => 'Quelle valeur de display n’existe pas ?',
                'answers' => [
                    'inline-table',
                    'inline-flex',
                    'inline-grid',
                    'inline-float',
                    'inline-block'
                ],
                'correct_answer' => 3
            ],
            [
                'question' => 'Quelle est la valeur par défaut de la propriété position ?',
                'answers' => [
                    'relative',
                    'none',
                    'auto',
                    'inherit',
                    'static'
                ],
                'correct_answer' => 4
            ],
            [
                'question' => 'Par défaut, dans quel ordre de priorité sont affectées les variables envoyées par HTTP ?',
                'answers' => [
                    'get, cookie, post',
                    'post, get, cookie',
                    'get, post, cookie',
                    'post,cookie, get'
                ],
                'correct_answer' => 2
            ],
            [
                'question' => 'Quelle fonction de bufferisation (temporisation) envoie au navigateur les données contenues par le tampon, et stoppe la tamporisation de sortie ?',
                'answers' => [
                    'ob_flush()',
                    'ob_end_flush()',
                    'ob_get_flush()'
                ],
                'correct_answer' => 1
            ],
            [
                'question' => 'Quelle méthode permet d\'être sûr d\'exécuter une instruction lorsque le DOM a été bien mis à jour ?',
                'answers' => [
                    'Vue.sync',
                    'Vue.nextTick',
                    'Vue.refresh',
                    'Vue.await'
                ],
                'correct_answer' => 1
            ],
            [
                'question' => 'Quelle variable spéciale contient l\'événement natif attrapé par v-on ?',
                'answers' => [
                    '$event',
                    'nativeEvent',
                    'e',
                    'e.native'
                ],
                'correct_answer' => 0
            ],
            [
                'question' => 'Quelle fonction retourne la longueur d\'une chaîne de texte ?',
                'answers' => [
                    'strlen',
                    'strlength',
                    'length',
                    'substr'
                ],
                'correct_answer' => 0
            ],
            [
                'question' => 'Quelle fonction permet d\'envoyer des en-têtes HTTP au navigateur avant le contenu de la page ?',
                'answers' => [
                    'parse_url()',
                    'http_post()',
                    'header()'
                ],
                'correct_answer' => 2
            ],
            [
                'question' => 'Quelle fonction permet d\'effacer un fichier ?',
                'answers' => [
                    'delete()',
                    'unlink()',
                    'remove()',
                    'clearfile()'
                ],
                'correct_answer' => 1
            ],
            [
                'question' => 'Quelles directives ne faut-il pas mélanger sur un même élément ou composant ?',
                'answers' => [
                    'v-text et v-on',
                    'v-show et v-model',
                    'v-for et v-if',

                ],
                'correct_answer' => 2
            ],

        ];
        $userAnswers = [];
        for ($i = 1; $i <= 20; $i++) {
            $userAnswers[] = $request->request->get('question'.$i);

        }

        $score = 0;
        foreach ($quiz as $key => $question) {
            if (isset($userAnswers[$key]))
            if ($question['correct_answer'] == $userAnswers[$key]) {
                $score+= 2;
            }
        }
       $user = $this->getUser();
        $user->setNote($score);
        $entityManager->flush();

       return $this->redirectToRoute('app_user_index',[]);

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
