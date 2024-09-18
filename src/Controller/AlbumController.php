<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    #[Route('/album', name: 'app_album', methods:("GET"))]
    public function listeAlbums(AlbumRepository $repo): Response
    {
        $albums=$repo->findBy(['date'=>'2006'],['nom'=>'asc']);
        return $this->render('album/listeAlbums.html.twig', [
            'lesAlbums' => $albums
        ]);
    }

    #[Route('/album/{id}', name: 'ficheAlbum', methods:("GET"))]
    public function ficheAlbum(Album $album): Response
    {
        return $this->render('album/ficheAlbum.html.twig', [
            'leAlbum' => $album
        ]);
    }
}
