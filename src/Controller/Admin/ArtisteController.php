<?php

namespace App\Controller\Admin;

use App\Entity\Artiste;
use App\Form\ArtisteType;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArtisteController extends AbstractController
{
    #[Route('/admin/artiste', name: 'app_admin_artiste', methods:("GET"))]
    public function listeArtistes(ArtisteRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $artistes= $paginator->paginate(
            $repo->listeArtistesCompletePaginee(),
            $request->query->getInt('page', 1), /*page number*/
            9 /*limit per page*/
        );
        return $this->render('admin/artiste/listeArtistes.html.twig', [
            'lesArtistes' => $artistes
        ]);

    }

    #[Route('/admin/artiste/ajout', name: 'app_admin_artiste_ajout', methods:['GET','POST'])]
    #[Route('/admin/artiste/modif/{id}', name: 'app_admin_artiste_modif', methods:['GET','POST'])]
    public function ajoutModifArtiste(Artiste $artiste=null, ArtisteRepository $repo, PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager): Response
    {
        if($artiste == null){
            $artiste=new Artiste();
            $mode="ajouté";
        } else{
            $mode="modifié";    
        }     
        $form=$this->createForm(ArtisteType::class, $artiste);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($artiste);
            $manager->flush();
            $this->addFlash("success", "L'artiste à bien été $mode !");
            return $this->redirectToRoute('app_admin_artiste');
        }

        return $this->render('admin/artiste/formAjoutModifArtiste.html.twig', [
            'formArtiste' => $form->createView()
        ]);

    }

    #[Route('/admin/artiste/suppression/{id}', name: 'app_admin_artiste_suppr', methods:("GET"))]
    public function SuppressionArtiste(Artiste $artiste, ArtisteRepository $repo, EntityManagerInterface $manager): Response
    {
        $nbAlbums=$artiste->getAlbums()->count();
        if($nbAlbums>0){
            $this->addFlash("danger", "Vous ne pouvez pas supprimer cet artiste car $nbAlbums album(s) y sont associé(s) ! ");
        }else{
            $manager->remove($artiste);   
            $manager->flush();
            $this->addFlash("success", "L'artiste à bien été supprimé !");
        }
      
        return $this->redirectToRoute('app_admin_artiste');

    }

    
}
