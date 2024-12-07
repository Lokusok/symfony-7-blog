<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {}

    #[Route('/articles', name: 'articles.index', methods: ['GET'])]
    public function index(): Response
    {
        $repository = $this->em->getRepository(Article::class);

        $articles = $repository->findBy([], orderBy: [
            'id' => 'DESC'
        ]);

        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/articles/create', name: 'articles.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $article->setTitle($data->getTitle());
            $article->setContent($data->getContent());

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'You successfully created article.');
            return $this->redirectToRoute('articles.index');
        }

        return $this->render('articles/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/articles/{id}/edit', name: 'articles.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $article->setTitle($data->getTitle());
            $article->setContent($data->getContent());

            $this->em->flush();

            $this->addFlash('success', 'You successfully edited this article.');

            return $this->redirectToRoute('articles.show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('articles/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/articles/{id}', name: 'articles.show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/articles/{id}', name: 'articles.destroy', methods: ['DELETE'])]
    public function destroy(Article $article)
    {
        $this->addFlash('success', "You successfully deleted article with id {$article->getId()}");

        $this->em->remove($article);
        $this->em->flush();

        return $this->redirectToRoute('articles.index');
    }
}
