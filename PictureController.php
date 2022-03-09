<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\ArticleRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/picture')]
class PictureController extends AbstractController
{
    #[Route('/', name: 'picture_index', methods: ['GET'])]
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'picture_new', methods: ['GET', 'POST'])]
    public function new($id, Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //________________debut modif

               // je récupére le fichies passé dans le form
               $image = $form->get('name')->getdata();
               // si il y a une image de chargée
               if ($image) {
                   // je crée un nom unique pour cette image et je remet l'extension
                   $img_file_name = uniqid() . '.' . $image->guessExtension();
                   // enregistrer le fichier dans le dossier image 
                   $image->move($this->getParameter('upload_dir'), $img_file_name);
                   // je set l'object article
                   $picture->setName($img_file_name);
               } else {
                   // si $image = null je set l'image par default
                   $picture->setName('defaultimg.jpg');
               }
                // ajouter la relation avec article
                $article= $articleRepository->find($id);
                // je set la valeur article de la table comment avec l'objet
                $picture->setArticle($article);

            //________________fin modif
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('article_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'picture_show', methods: ['GET'])]
    public function show(Picture $picture): Response
    {
        return $this->render('picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/{id}/edit', name: 'picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }


    #[Route('/supprimer/{id}', name: 'picture_delete')]
    public function delete(Request $request, Picture $picture, EntityManagerInterface $entityManager): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($picture);
            $entityManager->flush();
        //}
        // il faut récupérer l'id de l'article a le quel est lier l'image qui viens d'etre supprimer
        $id = ($picture->getArticle()->getId());

        return $this->redirectToRoute('article_show', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
