<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\All;

class HomeController extends AbstractController
{
   
    /**
     *
     *@Route("/", name="home")
     */
    public function home(ArticleRepository $repository)
    {   
        // Permet de récupérer tout les articles dans la BDD et de les classées par la date de création la plus récente
        $articles = $repository->findBy([],['date_created' => 'DESC']);
      
        return $this->render('home/home.html.twig', [
            'articles' => $articles
        ]);
    }

     /**
     *
     *@Route("/article/{id}{slug}", name="article")
     */
    public function article( Article $article)
    {   
        // On récupère la date de création
        $date = $article->getDateCreated();
        // On met là date au bon format
        $date=$date->format('d-m-Y H:i:s');
        //substr nous permet de récupérer une partie de la chaine de caractère dans la variable $date
        // On récupère dans une variable là date
        $jour = substr($date, 0, -8);
        // On récupère dans une variable l'heure
        $heure = substr($date, 11);
       
        return $this->render('home/article.html.twig', [
            'article' => $article,
            'jour'=> $jour,
            'heure'=>$heure,
        ]);
    }
}

