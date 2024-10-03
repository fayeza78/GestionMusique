<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    #[Route('/admin/album', name: 'app_admin_album', methods:("GET"))]
    public function listeAlbums(AlbumRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $albums= $paginator->paginate(
            $repo->listeAlbumsCompletePaginee(),
            $request->query->getInt('page', 1), /*page number*/
            9 /*limit per page*/
        );
        return $this->render('admin/album/listeAlbums.html.twig', [
            'lesAlbums' => $albums
        ]);

    }

    #[Route('/admin/album/ajout', name: 'app_admin_album_ajout', methods:['GET','POST'])]
    #[Route('/admin/album/modif/{id}', name: 'app_admin_album_modif', methods:['GET','POST'])]
    public function ajoutModifAlbum(Album $album=null, AlbumRepository $repo, PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager): Response
    {
        if($album == null){
            $album=new Album();
            $mode="ajouté";
        } else{
            $mode="modifié";    
        }     
        $form=$this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($album);
            $manager->flush();
            $this->addFlash("success", "L'album à bien été $mode !");
            return $this->redirectToRoute('app_admin_album');
        }

        return $this->render('admin/album/formAjoutModifAlbum.html.twig', [
            'formAlbum' => $form->createView()
        ]);

    }

    #[Route('/admin/album/suppression/{id}', name: 'app_admin_album_suppr', methods:("GET"))]
    public function SuppressionAlbum(Album $album, AlbumRepository $repo, EntityManagerInterface $manager): Response
    {
        $nbMorceaux=$album->getMorceaux()->count();
        if($nbMorceaux>0){
            $this->addFlash("danger", "Vous ne pouvez pas supprimer cet album car $nbMorceaux morceau(x) y sont associé(s) ! ");
        }else{
            $manager->remove($album);   
            $manager->flush();
            $this->addFlash("success", "L'album à bien été supprimé !");
        }
      
        return $this->redirectToRoute('app_admin_album');

    }
}
