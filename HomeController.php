<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    #[Route('/home', name: 'home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('home/index.html.twig', [
           'list_article' =>$articleRepository->findAll() 
            
        ]);
    }


 /**
     * @Route("/detail-article/{id}", name="detail_article")
     */
    public function show($id, ArticleRepository $articleRepository): Response
    {
        return $this->render('home/detail_article.html.twig', [
            'article' => $articleRepository->find($id),
            'form'=>$form->createView()
        ]);
    }


}
