<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\AddCategoryType;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        // je récupére la liste des category avec la méthode findAll() de l'object categoryrepository 
        $list_category = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [

            // j'envoi la liste a la vue 
            'list_category' => $list_category
        ]);
    }

   // ________________ ajouter un enregistrement ________________________

    /**
     * @Route("/ajouter-category", name="add_category")
     */
    public function newCategory(Request $request, EntityManagerInterface $manager): Response
    {

        // créer un object category vide
        $category = new Category();
        // je vais lier mon object $category avec le formulaire addCategortype.php
        $form = $this->createForm(AddCategoryType::class, $category);
        // je met mon formulaire a l'ecoute des request si y'a un $_post present 
        $form->handleRequest($request);
        // je vérifi si le formulaire a était validé
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            // apres je redirige vers 
            return $this->redirectToRoute('category');
        }

        return $this->render('category/new_category.html.twig', [

            // j'envoi le formulaire a la vue 
            'form' => $form->createView()
        ]);
    }


    //_______________Méthode pour modifier ________________________________
    /**
     * @Route("Modifier-category/{id}", name="edit_category")
     */
    public function editCategory(
        $id,
        Request $request,
        EntityManagerInterface $manager,
        CategoryRepository $categoryRepository
    ): Response {

        // je récupére l'enregistrement a modifier par son id
        $category = $categoryRepository->find($id);

        // je vais lier mon object $category avec le formulaire addCategortype.php
        $form = $this->createForm(AddCategoryType::class, $category);
        // je met mon formulaire a l'ecoute des request si y'a un $_post present 
        $form->handleRequest($request);
        // je vérifi si le formulaire a était validé
        if ($form->isSubmitted() && $form->isValid()) {
   
        

            $manager->persist($category);
            $manager->flush();
            // apres je redirige vers 
            return $this->redirectToRoute('category');
        }

        return $this->render('category/edit_category.html.twig', [

            // j'envoi le formulaire a la vue 
            'form' => $form->createView()
        ]);
    }

     //_______________Méthode pour voir un enregistrement ________________________________
    /**
     * @Route("voir-category/{id}", name="show_category")
     */
    public function showCategory( $id, CategoryRepository $categoryRepository  ): Response {

        // je récupére l'enregistrement a voir par son id
        $category = $categoryRepository->find($id);

        return $this->render('category/show_category.html.twig', [
           // j'envoie l'enregistrement a la vue 
             'category' => $category
        ]);
    }

      //_______________Méthode pour suuprimerr un enregistrement ________________________________
    /**
     * @Route("supprimer-category/{id}", name="delete_category")
     */
    public function deleteCategory( $id, 
                CategoryRepository $categoryRepository,
                EntityManagerInterface $manager  ): Response {

        // je récupére l'enregistrement a voir par son id
        $category = $categoryRepository->find($id);
        $manager->remove($category);
        $manager->flush();

        return $this->redirectToRoute('category');

      
    }


}
