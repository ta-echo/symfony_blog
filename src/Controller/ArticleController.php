<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // je récupére le fichies passé dans le form
            $image = $form->get('image')->getdata();
            // si il y a une image de chargée
            if ($image) {
                // je crée un nom unique pour cette image et je remet l'extension
                $img_file_name = uniqid() . '.' . $image->guessExtension();
                // enregistrer le fichier dans le dossier image 
                $image->move($this->getParameter('upload_dir'), $img_file_name);
                // je set l'object article
                $article->setImage($img_file_name);
            } else {
                // si $image = null je set l'image par default
                $article->setImage('defaultimg.jpg');
            }


            // je récupére la date du jour 
            $date_jour = new DateTime();
            // je set la propriété date_pub de l'object article avant d'enregistrer en BDD 
            $article->setDatePub($date_jour);
            // je set la propriété auteur avec 'admin' 
            $auteur = 'admin';
            $article->setAuteur($auteur);

            // j'enregistre en BDD 
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {

        // je récupére l'ancien nom de l'image avant qu'il ne soit effacé
        $old_name_img = $article->getImage();
        // création du formulaire
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // je récupére le fichies passé dans le form
            $image = $form->get('image')->getdata();
            // si il y a une image de chargée
            if ($image) {
                // je crée un nom unique pour cette image et je remet l'extension
                $img_file_name = uniqid() . '.' . $image->guessExtension();
                // enregistrer le fichier dans le dossier image 
                $image->move($this->getParameter('upload_dir'), $img_file_name);
                // je set l'object article
                $article->setImage($img_file_name);
                $name_file_delete = $this->getParameter('upload_dir') . $old_name_img;
                if (file_exists($name_file_delete) && is_file($name_file_delete)) {
                    unlink($name_file_delete);
                }
            } else {
                // si $image = null je setavec l'ancien nom 
                $article->setImage($old_name_img);
            }

            //________________ envoi a la BDD __________________________
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();

            $old_name_img = $article->getImage();
            if ($old_name_img != 'defaultimg.jpg') {
                $name_file_delete = $this->getParameter('upload_dir') . $old_name_img;
                if (file_exists($name_file_delete) && is_file($name_file_delete)) {
                    unlink($name_file_delete);
                }
            }
        }



        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }
}
