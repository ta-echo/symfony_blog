<?php

namespace App\Controller;

use App\Entity\NomUser;
use App\Form\NomUserType;
use App\Repository\NomUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nom/user')]
class NomUserController extends AbstractController
{
    #[Route('/', name: 'nom_user_index', methods: ['GET'])]
    public function index(NomUserRepository $nomUserRepository): Response
    {
        return $this->render('nom_user/index.html.twig', [
            'nom_users' => $nomUserRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'nom_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nomUser = new NomUser();
        $form = $this->createForm(NomUserType::class, $nomUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             //_____________ Début de code pour enregistrer une image _______________
              // je récupére le fichies passé dans le form
             $image = $form->get('avatar')->getdata();
             // si il y a une image de chargée
             if ($image) {
                 // je crée un nom unique pour cette image et je remet l'extension
                 $img_file_name = uniqid() . '.' . $image->guessExtension();
                 // enregistrer le fichier dans le dossier image 
                 $image->move($this->getParameter('upload_dir'), $img_file_name);
                 // je set l'object article
                 $nomUser->setAvatar($img_file_name);
             } else {
                 // si $image = null je set l'image par default
                 $nomUser->setAvatar('defaultimg.jpg');
             }

             //___________ Fin de code pour enregistrer une image _______________
            $entityManager->persist($nomUser);
            $entityManager->flush();

            return $this->redirectToRoute('nom_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nom_user/new.html.twig', [
            'nom_user' => $nomUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'nom_user_show', methods: ['GET'])]
    public function show(NomUser $nomUser): Response
    {
        return $this->render('nom_user/show.html.twig', [
            'nom_user' => $nomUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'nom_user_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, NomUser $nomUser, EntityManagerInterface $entityManager): Response
    {
        $old_name_img = $nomUser->getAvatar();
        $form = $this->createForm(NomUserType::class, $nomUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // code debut pour avatar___________________
        // je récupére le fichies passe dans le form
            $image = $form->get('avatar')->getdata();
            // si il y a une image de chargée
            if ($image) {
                // je crée un nom unique pour cette image et je remet l'extension
                $img_file_name = uniqid() . '.' . $image->guessExtension();
                // enregistrer le fichier dans le dossier image 
                $image->move($this->getParameter('upload_dir'), $img_file_name);
                // je set l'object article
                $nomUser->setAvatar($img_file_name);
                $name_file_delete = $this->getParameter('upload_dir') . $old_name_img;
                if (file_exists($name_file_delete) && is_file($name_file_delete)) {
                    unlink($name_file_delete);
                }
            } else {
                // si $image = null je setavec l'ancien nom 
                $nomUser->setAvatar($old_name_img);
            }

            // code fin pour avatar_________________
            $entityManager->flush();

            return $this->redirectToRoute('nom_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nom_user/edit.html.twig', [
            'nom_user' => $nomUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'nom_user_delete', methods: ['POST'])]
    public function delete(Request $request, NomUser $nomUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nomUser->getId(), $request->request->get('_token'))) {
            $entityManager->remove($nomUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('nom_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
