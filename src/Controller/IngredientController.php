<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     *
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(),  //$query, requete!! 
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig',['ingredients'=>$ingredients]);
    }

    /**
     * This controller show a form which create an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    #[Route('/ingredient/nouveau', name:'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient;
        $form= $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request); //attrape la requete
        if($form->isSubmitted() && $form->isValid())
        {
            $ingredient=$form->getData(); // dd($ingredient);
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success', //acorde con Bootstrap
                'Votre ingrédient a été crée avec succes!'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', ['form'=>$form->createView()]); //creer form pour la vue
    }

    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods:['GET','POST'])] //ambos porque habra un formulaire
    public function edit (Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        $form= $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request); //attrape la requete
        if($form->isSubmitted() && $form->isValid())
        {
            $ingredient=$form->getData(); // dd($ingredient);
            $manager->persist($ingredient); //para guardar en BDD
            $manager->flush();              //lo envia a BDD

            $this->addFlash(
                'success', //acorde con Bootstrap
                'Votre ingrédient a été modifié avec succes!'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', ['form'=> $form->createView()]);
    }

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods:['GET'])]
    public function delete (EntityManagerInterface $manager, Ingredient $ingredient): Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success', //acorde con Bootstrap
            'Votre ingrédient a été supprimé avec succes!'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
