<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/gestion", name="gestion")
     */
    public function gestion(ArticleRepository $repository)
    {
        $articles = $repository->findBy([],['date_created' => 'DESC']);
        return $this->render('admin/gestionArticles.html.twig', [
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/addArticle", name="addArticle")
     * 
     */
    public function addArticle(Request $request, EntityManagerInterface $manager)
    {
        $article = new Article();
        // Création du formulaire d'ajout d'article
        $form = $this->createForm(ArticleType::class, $article, ['add' => true]);
        $form->handleRequest($request);
    
        // Vérification de la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Permet de remplacer les sauts de ligne par une balise </ br> dans le texte
            $text=nl2br($article->getContent());
            $article->setContent($text);

            //On set la date de création de l'article
            $article->setDateCreated(new \DateTime('now'));
            $date=$article->getDateCreated();
            // Transformation de la date du décimale
            $article->setDateString($date->format('U'));
            $manager->persist($article);

            // on récupère ici toutes les données de la cover
            $file = $form->get('cover')->getData();

            //ici on place une condition pour vérifier qu'une photo a bien été uploadée
            if ($file) {
                $fileName = date('YmdHis') . '-' . uniqid() . '-' . $file->getClientOriginalName();

                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    // la méthode move() attend 2 paramètres et permet de déplacer le fichier uploader temp du serveur vers un emplacement défini
                    // param1: l'emplacement défini, paramétré au préalable dans config/services.yaml
                    // upload_directory : '%kernel.ptoject_dir%/assets/upload'
                    //param2 : le nom du fichier à déplacer
                } catch (FileException $exception) {
                    $this->redirectToRoute('addProduct', [
                        'erreur' => $exception
                    ]);
                }
                // l'objet $product n'étant pas setté sur l'information cover (picture étant un input type file et les données attendues en BDD étant de type string=>le nom du fichier)
                $article->setCover($fileName);

                // On demande au manager de Doctrine (ORM) de préparer la requête 
                $manager->persist($article);

                // On éxécute la ou les requêtes 
                $manager->flush();

                return $this->redirectToRoute('home');
            }
        }
        
        return $this->render('admin/addArticle.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajout de produit'
        ]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     * 
     */
    public function modifier(Request $request, EntityManagerInterface $manager, Article $article)
    {
        // Crée un formulaire pour éditer le menu
        $form = $this->createForm(ArticleType::class, $article,['edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Même procédure que pour l'ajout d'un article
            $article->setDateCreated(new \DateTime('now'));
            $date=$article->getDateCreated();
            $article->setDateString($date->format('U'));
            $text=nl2br($article->getContent());
            $article->setContent($text);
            $manager->persist($article);
            // Récupère le nom de fichier de la nouvelle photo
            $file = $form->get('editCover')->getData();
            // Remplace l'ancienne photo et le nom de fichier de l'ancienne photo par le nouveau
            if ($file) {
                $fileName = date('YmdHis') . '-' . uniqid() . '-' . $file->getClientOriginalName();

                try {
                    $file->move($this->getParameter('upload_directory'), $fileName);
                    unlink($this->getParameter('upload_directory') . '/' . $article->getCover());
                } 
                catch (FileException $exception) {
                    $this->redirectToRoute('modifier', [
                        'erreur' => $exception
                    ]);
                }
                $article->setCover($fileName);
            }
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('gestion');
        }
        return $this->render('admin/modifier.html.twig', [
            'form' => $form->createView(),
            'titre' => "Modification de l'article",
            'article' => $article
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     * 
     */
    public function supprimer(EntityManagerInterface $manager, Article $article)
    {   
        // Sépare le fichier photo du dossier "upload_directory"
        unlink($this->getParameter('upload_directory') . '/' . $article->getCover());
        // Suppression de l'article
        $manager->remove($article);
        $manager->flush();
        return $this->redirectToRoute('gestion');
    }
}
