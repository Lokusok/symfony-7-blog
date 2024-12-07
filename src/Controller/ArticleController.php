<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'articles.index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Article::class);

        $articles = $repository->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/articles/create', name: 'articles.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $article->setTitle($data->getTitle());
            $article->setContent($data->getContent());

            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'You successfully created article.');
            return $this->redirectToRoute('articles.index');
        }

        return $this->render('articles/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/articles/{id}/edit', name: 'articles.edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $em, Request $request, int $id): Response
    {
        $repository = $em->getRepository(Article::class);

        $article = $repository->find($id);

        if (! $article) {
            return $this->createNotFoundException('Article not found :(');
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $article->setTitle($data->getTitle());
            $article->setContent($data->getContent());

            $em->flush();

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
    public function show(EntityManagerInterface $em, int $id): Response
    {
        $repository = $em->getRepository(Article::class);

        $article = $repository->find($id);

        if (! $article) {
            return $this->createNotFoundException('Article not found :(');
        }

        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/articles/{id}', name: 'articles.destroy', methods: ['DELETE'])]
    public function destroy(EntityManagerInterface $em, int $id)
    {
        $repository = $em->getRepository(Article::class);

        $article = $repository->find($id);

        if (! $article) {
            return $this->createNotFoundException('Article not found :(');
        }

        $this->addFlash('success', "You successfully deleted article with id {$article->getId()}");

        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('articles.index');
    }
}
