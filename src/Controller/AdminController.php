<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $usersRepository)
    {
        $this->userRepository = $usersRepository;
    }

    /**
     * @Route("/admin", name="admin_index")
     */
    public function index()
    {
        $users = $this->userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'user' => $users,
            'title' => 'Liste des participants'
        ]);
    }

    /**
     * @Route("/admin/new", name="new_user_admin")
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/show/{id}", name="show_user_admin", methods={"GET"})
     */
    public function show_user(User $user)
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/edit/{id}", name="edit_user_admin", methods={"GET","POST"})
     */
    public function edit_user(Request $request, User $user): Response
    {

        $form = $this->createForm(UserType::class, $user);
        $form->remove('password');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setDateUpdate(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('edit_user_admin' , ['id'=>$user->getid()]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

