<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{


    #[Route("/user-get-data", name: 'user_get_data')]
    public function getData(Request $request, UserRepository $userRepository): Response
    {
        $draw = $request->query->get('draw');
        $start = $request->query->get('start');
        $length = $request->query->get('length');

        $query = $userRepository->createQueryBuilder('u');

        // Filters
        if ($request->query->get('role')) {
            $query->andWhere('u.roles = :role')
                ->setParameter('role', $request->query->get('role'));
        }
        if ($request->query->get('status') && $request->query->get('status') === "inactive") {
            $query->andWhere('u.status = 0');
        } else if ($request->query->get('status') && $request->query->get('status') === "active") {
            $query->andWhere('u.status = 1');
        }
        if ($request->query->get('start_date') && $request->query->get('end_date')) {
            $startDate = new \DateTime($request->query->get('start_date'));
            $endDate = (new \DateTime($request->query->get('end_date')))->setTime(23, 59, 59);
            $query->andWhere('u.created_at BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }

        // Get total records count
        $totalRecords = count($query->getQuery()->getResult());

        // Add limit
        $query->setFirstResult($start)
            ->setMaxResults($length);

        $users = $query->getQuery()->getResult();

        $data = [];
        // Return data as JSON
        foreach ($users as $key => $user) {
            $data[] = [
                'id' => $key + 1,
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getlastName(),
                'email' => $user->getEmail(),
                'age' => $user->getage(),
                'hobby' => $user->getHobbyList(),
                'gender' => $user->getGender()->name,
                'status' => $user->isStatus() ? "Active" : "Inactive",
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'roles' => $user->getRoles()->name,
                'actions' => '<a class="btn btn-primary" href=' . $this->generateUrl('app_user_show', ['id' => $user->getId()]) . '">Show</a> | <a class="btn btn-success" href="' . $this->generateUrl('app_user_edit', ['id' => $user->getId()]) . '">Edit</a> | 
                <form method="post" action="' . $this->generateUrl('app_user_delete', ['id' => $user->getId()]) . '" onsubmit="return confirm(\'Are you sure you want to delete this item?\');">
                <button class="btn btn-danger">Delete</button>
                </form>
                '
            ];
        }
        return $this->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'User created successfully!'
            );
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->addFlash(
            'success',
            'User display successfully!'
        );
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash(
                'success',
                'User updated successfully!'
            );
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'User deleted successfully!'
        );
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
