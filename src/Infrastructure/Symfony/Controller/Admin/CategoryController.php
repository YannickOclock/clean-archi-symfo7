<?php

namespace App\Infrastructure\Symfony\Controller\Admin;

use App\Infrastructure\Symfony\Controller\Admin\Trait\BreadcrumbTrait;
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Infrastructure\Symfony\Repository\CategoryRepository;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories', name: 'admin_categories_')]
class CategoryController extends AbstractController
{
    use BreadcrumbTrait;

    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $categories = $categoryRepository->findCategoriesPaginated($page, 50);
        return $this->render('admin/catalog/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/index/{id}', name: 'sub_index')]
    public function subIndex(Category $category, CategoryRepository $categoryRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $categories = $categoryRepository->findSubCategoriesPaginated($category->getId(), $page, 50);
        $categories['breadcrumbs'] = $this->getBreadCrumbs($categories["data"][0]);
        return $this->render('admin/catalog/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, CategoryRepository $categoryRepository): Response
    {
        // uniquement autorisé pour les administrateurs
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on crée une nouvelle catégorie
        $category = new Category();

        // on crée le formulaire
        $categoryForm = $this->createForm(CategoryFormType::class, $category);
        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            // on génère le slug
            $slug = $slugger->slug($category->getName());
            $slug = strtolower($slug);
            $category->setSlug($slug);
            // TODO: On calculera automatiquement la position à mettre ici
            // valeur par défaut de la position
            $categoryOrder = 1;
            // si on a sélectionner une catégorie dans le parent
            $parent = $category->getParent();
            //dd($parent);
            if($parent !== null) {
                // on va chercher les sous catégories (pour chercher le dernier de la liste)
                $subCategories = $categoryRepository->findByParent($parent);
                foreach($subCategories as $subCategory) {
                    if($subCategory->getCategoryOrder() > $categoryOrder) {
                        $categoryOrder = $subCategory->getCategoryOrder();
                    }
                }
                // si on a trouvé des catégories, on incrémente la position
                if($categoryOrder > 1) {
                    $categoryOrder++;
                } else {
                    // TODO: à garder juste pour les tests (partie à supprimer)
                    $categoryOrder = $parent->getCategoryOrder() + 1;
                }
            } else {
                // on va chercher la dernière catégorie de niveau 1 rajoutée
                // npCategory => no parent category
                $npCategories = $categoryRepository->findBy(['parent' => null]);
                foreach($npCategories as $npCategory) {
                    if($npCategory->getCategoryOrder() > $categoryOrder) {
                        $categoryOrder = $npCategory->getCategoryOrder();
                    }
                }
                // si on a trouvé des catégories sans parent, on incrémente la position
                // TODO: à garder juste pour les tests en dev (10 à remplacer par 1)
                $categoryOrder = ($categoryOrder > 1) ? $categoryOrder + 10 : 10;
            }
            $category->setCategoryOrder($categoryOrder);

            // On stocke les informations
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès');
            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/catalog/categories/add.html.twig', [
            'categoryForm' => $categoryForm
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        // vérifier si l'utilisateur peut éditer avec le voter
        $this->denyAccessUnlessGranted('CATEGORY_EDIT', $category);

        // on crée le formulaire
        $categoryForm = $this->createForm(CategoryFormType::class, $category);
        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            // On récupère l'image de la catégorie
            $image = $categoryForm->get('image')->getData();
            // upload de l'image
            $folder = 'categories';
            $fichier = $pictureService->add($image, $folder, 250, 250);
            $category->setImageName($fichier);
            // on génère le slug
            $slug = $slugger->slug($category->getName());
            $slug = strtolower($slug);
            $category->setSlug($slug);
            // On stocke les informations
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie modifié avec succès');
            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/catalog/categories/edit.html.twig', [
            'categoryForm' => $categoryForm,
            'category' => $category
        ]);
    }

    #[Route('/edition/en_ligne/{id}', name: 'edit_is_published', methods: ['PUT'])]
    public function togglePublished(Category $category, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // On récupère le contenu de la requête
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('editPublished' . $category->getId(), $data['_token'])) {
            // le token csrf est valide
            // on mets à jour le paramètre isVerified
            $isPublished = $category->isPublished();
            $category->setPublished(!$isPublished);

            // S'il y a des catégories enfants, il faut aussi changer le statut des catégories enfant
            foreach($category->getCategories() as $subCategory) {
                $subCategory->setPublished(!$isPublished);
                $em->persist($subCategory);
            }

            $em->persist($category);
            $em->flush();

            $actionTxt = (!$isPublished) ? 'activée' : 'désactivée';
            $messageTxt = "Catégorie {$category->getId()} {$actionTxt} avec succès !";

            return new JsonResponse(['success' => true, "message" => $messageTxt], 200);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }

    #[Route('/change/position/{id}/{position}', name: 'change_position', methods: ['PUT'], requirements: ["position" => "up|down"])]
    public function changePosition(Category $category, String $position, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $em): JsonResponse
    {
        // On récupère le contenu de la requête
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('changePosition' . $category->getId(), $data['_token'])) {
            // le token csrf est valide
            // on mets à jour le paramètre isVerified
            // todo il faut coder la partie changement de position

            $categoryOrder = $category->getCategoryOrder();
            $categoryParent = $category->getParent()?->getId();

            $nextCategories = $categoryRepository->findPreviousOrNextCategoryInSameLevel($position, $categoryOrder, $categoryParent);
            $nextOrPreviousCategory = $nextCategories[0];

            // sauvegarde de l'ancienne position
            $categoryOrderTmp = $category->getCategoryOrder();

            // mise à jour des positions
            $category->setCategoryOrder($nextOrPreviousCategory->getCategoryOrder());
            $nextOrPreviousCategory->setCategoryOrder($categoryOrderTmp);
            $em->persist($category);
            $em->persist($nextOrPreviousCategory);
            $em->flush();

            $actionTxt = ($position === "up") ? "montée":"descendue";
            $messageTxt = "Catégorie {$category->getId()} {$actionTxt} avec succès !";

            return new JsonResponse(
                [
                    'success' => true,
                    "message" => $messageTxt,
                    "position" => $position,
                    "nextOrPreviousCategoryId" => $nextOrPreviousCategory->getId(),
                    "nextOrPreviousCategoryOrder" => $nextOrPreviousCategory->getCategoryOrder(),
                    "categoryId" => $category->getId(),
                    "categoryOrder" => $category->getCategoryOrder(),
                ],
                200
            );
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}