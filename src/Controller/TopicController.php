<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Topic;
use App\Form\ReponseType;
use App\Form\SearchType;
use App\Form\TopicType;
use App\Repository\TopicRepository;
use Knp\Component\Pager\PaginatorInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/topic")
 */
class TopicController extends AbstractController
{
    /**
     * @Route("/", name="topic_index", methods={"GET"})
     */
    public function index(TopicRepository $topicRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $topics = $topicRepository->findBy([], ['createdAt' => 'desc']);

        $topicsPaginated = $paginator->paginate(
            $topics,
            $request->query->getInt('page', 1),
            10 /*limite d'article par page*/
        );
        return $this->render('topic/index.html.twig', [
            'topics' => $topicsPaginated
        ]);
    }

    /**
     * @Route("/new", name="topic_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setUser($this->getUser());
            $topic->setCreatedAt(new DateTime());
            $topic->setUpdatedAt(new Datetime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute('topic_index');
        }

        return $this->render('topic/new.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search", name="topic_search", methods={"GET","POST"})
     */
    public function search(Request $request, TopicRepository $topicRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $search = $data['search'];

            $topics = $topicRepository->search($search);

            $topicsPaginated = $paginator->paginate(
                $topics,
                $request->query->getInt('page', 1),
                10 /*limite d'article par page*/
            );

            return $this->render('topic/index.html.twig', [
                'topics' => $topicsPaginated
            ]);
        }

        return $this->render('topic/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="topic_show", methods={"GET"})
     */
    public function show(Topic $topic): Response
    {
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="topic_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Topic $topic): Response
    {
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setUpdatedAt(new Datetime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('topic_index');
        }

        return $this->render('topic/edit.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="topic_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Topic $topic): Response
    {
        if ($this->isCsrfTokenValid('delete'.$topic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index');
    }

    /**
     * @Route("/{id}/repondre", name="topic_repondre", methods={"GET","POST"})
     */
    public function repondre(Request $request, Topic $topic): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setUser($this->getUser());
            $reponse->setCreatedAt(new DateTime());
            $reponse->setTopic($topic);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('topic_show', [
                'id' => $topic->getId()
            ]);
        }

        return $this->render('topic/repondre.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }
}
