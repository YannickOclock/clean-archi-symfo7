<?php

namespace App\Infrastructure\Symfony\Controller\Admin;

use App\Infrastructure\Symfony\Entity\User;
use App\Infrastructure\Symfony\Form\Admin\UserFormType;
use App\Infrastructure\Symfony\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $users = $userRepository->findUsersPaginated($page, 1);

        return $this->render('admin/clients/users/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        // vérifier si l'utilisateur peut éditer avec le voter
        $this->denyAccessUnlessGranted('USER_EDIT', $user);

        // on crée le formulaire
        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()) {
            // si le mot de passe a été modifié, il faut l'encoder
            $plainPassword = $userForm->get('plainPassword')->getData();
            if ($plainPassword !== null) {
                $user->setPassword(
                    $userPasswordHasherInterface->hashPassword($user, $plainPassword)
                );
            }

            // On stocke les informations
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/clients/users/edit.html.twig', [
            'userForm' => $userForm,
            'user' => $user
        ]);
    }

    #[Route('/edition/verification/{id}', name: 'edit_is_verified', methods: ['PUT'])]
    public function toggleIsVerified(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // On récupère le contenu de la requête
        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('editIsVerified' . $user->getId(), $data['_token'])) {
            // le token csrf est valide
            // on mets à jour le paramètre isVerified
            $isVerified = $user->isVerified();
            $user->setIsVerified(!$isVerified);
            $em->persist($user);
            $em->flush();

            $actionTxt = (!$isVerified) ? 'activé' : 'désactivé';
            $messageTxt = "Utilisateur {$user->getId()} {$actionTxt} avec succès !";

            return new JsonResponse(['success' => true, "message" => $messageTxt], 200);
        }

        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}