<?php

namespace App\Controller;

use App\Entity\RssFeed;
use App\Form\RssFeedType;
use App\Repository\RssFeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rss/feed')]
final class RssFeedController extends AbstractController
{
    #[Route(name: 'app_rss_feed_index', methods: ['GET'])]
    public function index(RssFeedRepository $rssFeedRepository): Response
    {
        return $this->render('rss_feed/index.html.twig', [
            'rss_feeds' => $rssFeedRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rss_feed_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rssFeed = new RssFeed();
        $form = $this->createForm(RssFeedType::class, $rssFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rssFeed);
            $entityManager->flush();

            return $this->redirectToRoute('app_rss_feed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rss_feed/new.html.twig', [
            'rss_feed' => $rssFeed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rss_feed_show', methods: ['GET'])]
    public function show(RssFeed $rssFeed): Response
    {
        return $this->render('rss_feed/show.html.twig', [
            'rss_feed' => $rssFeed,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rss_feed_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RssFeed $rssFeed, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RssFeedType::class, $rssFeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rss_feed_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rss_feed/edit.html.twig', [
            'rss_feed' => $rssFeed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rss_feed_delete', methods: ['POST'])]
    public function delete(Request $request, RssFeed $rssFeed, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rssFeed->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rssFeed);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rss_feed_index', [], Response::HTTP_SEE_OTHER);
    }
}
