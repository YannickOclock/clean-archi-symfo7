<?php

namespace App\Infrastructure\Symfony\Controller\Admin;

use App\Infrastructure\Symfony\Entity\ProductImage;
use App\Infrastructure\Symfony\Entity\Product;
use App\Infrastructure\Symfony\Form\Admin\ProductFormType;
use App\Infrastructure\Symfony\Repository\ProductRepository;
use App\Infrastructure\Symfony\Service\Admin\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'admin_products_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $products = $productRepository->findProductsPaginated($page, 20);

        return $this->render('admin/catalog/products/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        // uniquement autorisé pour les administrateurs
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on crée un nouveau produit
        $product = new Product();

        // on crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);
        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid()) {

            // on génère le slug
            $slug = $slugger->slug($product->getName());
            $slug = strtolower($slug);
            $product->setSlug($slug);
            // On arrondit le prix
            //$price = $product->getPrice() * 100;
            //$product->setPrice($price);

            // Je persiste une première fois le produit avant les images
            // pour être sûr de ne pas avoir de doublon sur le slug !
            $em->persist($product);
            $em->flush();

            // On récupère les images
            $images = $productForm->get('images')->getData();
            foreach($images as $image) {
                $folder = 'products';
                $imageName = $pictureService->add($image, $folder, 250, 250);
                $productImage = new ProductImage();
                $productImage->setName($imageName);
                $product->addImage($productImage);
            }
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/catalog/products/add.html.twig', [
            'productForm' => $productForm
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        // vérifier si l'utilisateur peut éditer avec le voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        // on crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);
        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid()) {
            // On récupère les images
            $images = $productForm->get('images')->getData();
            foreach($images as $image) {
                $folder = 'products';
                $imageName = $pictureService->add($image, $folder, 250, 250);
                $productImage = new ProductImage();
                $productImage->setName($imageName);
                $product->addImage($productImage);
            }
            // on génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            $slug = strtolower($slug);
            // on met à jour la date de modification
            $product->setUpdatedAt(new \DateTimeImmutable());
            // On stocke les informations
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/catalog/products/edit.html.twig', [
            'productForm' => $productForm,
            'product' => $product
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
    }

    #[Route('/api/multiple/suppression', name: 'api_multi_delete_product', methods: ['PUT'])]
    public function apiMultiDelete(Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {
    }

    #[Route('/api/suppression/{id}', name: 'api_delete_product', methods: ['PUT'])]
    public function apiDelete(Product $product, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {
    }

    #[Route('/api/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function apiDeleteImage(ProductImage $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {
        // On récupère le contenu de la requête
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            // le token csrf est valide
            // on récupère le nom de l'image
            $nom = $image->getName();

            if($pictureService->delete($nom, 'products', 250, 250)) {
                $em->remove($image);
                $em->flush();

                return new JsonResponse(['success' => true], 200);
            }

            // La suppression a échouée
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }

    #[Route('/edition/en_ligne/{id}', name: 'edit_is_published', methods: ['PUT'])]
    public function togglePublished(Product $product, Request $request, EntityManagerInterface $em): JsonResponse
    {
    }
}