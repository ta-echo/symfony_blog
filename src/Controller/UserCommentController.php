<?php

namespace App\Controller;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\NomUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserCommentController extends AbstractController
{
    #[Route('/user/comment', name: 'user_comment')]
    public function index(): Response
    {
        return $this->render('user_comment/index.html.twig', [
        
        ]);
    }



#[Route('/new-user-commentaire/{id}', name:'comment_user_new', methods: ['GET', 'POST'])]

public function new($id,Request $request, EntityManagerInterface $entityManager, NomUserRepository $userRepository,
                    ArticleRepository $articleRepository): Response
{
    $comment = new Comment();
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        //_______________debut de mon code


            // je récupére la date du jour 
            $date_jour = new \DateTime();
            // je set la propriété date_message de l'object article avant d'enregistrer en BDD 
            $comment->setDateMessage($date_jour);
            // ajouter le user
            // $user - $this=>getUser();
            // pour le test je récupére un user de la table user
            $nom_user = $userRepository->find(2);  // recupérer id2
            $comment->setUser($nom_user);
            // ajouter la relation avec article
            $article= $articleRepository->find($id);
            // je set la valeur article de la table comment avec l'objet
            $comment->setArticle($article);

        //_______________fin de mon code
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('detail_article', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('user_comment/index.html.twig', [
        'comment' => $comment,
        'form' => $form->createView(), //ajouté mais test pas encore ->createView()
    ]);
    }
}
