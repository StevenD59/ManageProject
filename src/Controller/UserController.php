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


class UserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $usersRepository)
    {
        $this->userRepository = $usersRepository;
    }


    //    -------------- CRUD USER / INDEX-SHOWALL ----------------------

    /**
     * @Route("admin/users", name="admin_index")
     */
    public function index()
    {
        $users = $this->userRepository->findAll();
        return $this->render('admin/user/index.html.twig', [
            'user' => $users,
            'title' => 'Liste des participants'
        ]);
    }

    //    -------------- CRUD USER / CREATE ----------------------

    /**
     * @Route("admin/users/new", name="new_user_admin")
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

    //    -------------- CRUD USER / INDEX-SHOWById ----------------------

    /**
     * @Route("admin/user/show/{id}", name="show_user_admin", methods={"GET"})
     */
    public function show_user(User $user)
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user
        ]);
    }


    //    -------------- CRUD USER / EDIT ----------------------

    /**
     * @Route("admin/user/edit/{id}", name="edit_user_admin", methods={"GET","POST"})
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

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    //    -------------- CRUD USER / DELETE ----------------------

    /**
     * @Route("admin/delete/{id}", name="delete_user_admin", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }else{
            dd($request);
        }
        return $this->redirectToRoute('admin_index');
    }


    // ---------------------------- CONNEXION ---------------------------------------------

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('admin_index');
         }
        return $this->render('admin/user/login.html.twig');
    }
    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout() { }

}

