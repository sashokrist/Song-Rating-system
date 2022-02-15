<?php

namespace App\Controller;

use App\Entity\Rate;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SongController extends AbstractController
{
    /**
     * @Route("/song", name="song")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
       $user = get_current_user();
        $songs =$entityManager->getRepository(Song::class)->findAll();
        $sum = 0;
        foreach($songs as $a){
            $sum = 0;
        foreach($a->getRate() as $r){
            $sum += $r->getPoints();
        }
        $a->sum = $sum;
    }
        return $this->render('song/index.html.twig', [
            'songs' => $songs,
            'user' => $user
        ]);
    }

    /**
     * @Route("/song/new")
     * @throws \Doctrine\ORM\ORMException
     */
    public function new(EntityManagerInterface $entityManager)
    {
        $song = new Song();
        $song->setSongName('Go go go');
        $song->setBand('Cool Band');

        $entityManager->persist($song);
        $entityManager->flush();
        return new Response('name of the new song is: '.' '.$song->getSongName().' from band: '.$song->getBand());
    }

    /**
     * @Route("/song/{id}/vote", name="app_song_vote", methods="POST")
     */
    public function songVote(Song $song, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
      //  dd($user->getId());
       // $user = get_current_user();
        $vote = new Rate();
        $total = 100;
        $vote->setPoints($request->request->get('vote'));
        $vote->setUserId($user->getId());
        $vote->setSong($song);

        $entityManager->persist($song);
        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('song'));
        // return new Response('You gave: '.' '.$vote.' points to song- '.$song->getName());
    }
}
