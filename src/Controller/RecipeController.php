<?php

namespace App\Controller;

use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[NoReturn] #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {

        $recipes = $repository->findWithDurationLowerThan(30);
           return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * @param Request          $request
     * @param string           $slug
     * @param int              $id
     * @param RecipeRepository $repository
     *
     * @return Response
     */
    #[NoReturn] #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if ($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    /**
     * @param Recipe                 $recipe
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    #[NoReturn] #[Route('/recettes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdateAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    /**
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return RedirectResponse|Response
     */
    #[Route('/recettes/create', name: 'recipe.create')]
    public function create (Request $request, EntityManagerInterface $em): RedirectResponse|Response
    {
        $recipe = new recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdateAt(new \DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été créée');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param Recipe                 $recipe
     * @param EntityManagerInterface $em
     */
    #[Route('/recettes/{id}', name: 'recipe.delete', methods: ['DELETE'])]
        public function remove(Recipe $recipe, EntityManagerInterface $em)
        {
            $em->remove($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été supprimée');
            return $this->redirectToRoute('recipe.index');
    }


}
