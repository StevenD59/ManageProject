<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Project;
use App\Entity\User;
use App\Form\CommentaireType;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;



class ProjectController extends AbstractController
{

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

//    ------------------------------- CRUD PROJECT / ADMIN ---------------------------------

    /**
     * @Route("/projects", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        $project = $this->projectRepository->findAll();

        return $this->render('admin/project/index.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("admin/project/new", name="project_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, UserRepository $userRepository, UserInterface $userTest): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $file = $form->get('image_name')->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);
            $project->setImageName($fileName);
            $userId = $userTest->getId();
            $user = $userRepository->find($userId);
            $project->setUser($user);
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('admin/project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("project/show/{id}", name="project_show", methods={"GET","POST"})
     */
        public function show(Project $project, UserRepository $userRepository, Request $request): Response
    {

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        $users = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $commentaire->setProject($project);
            $commentaire->setUser($users);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('project_show',[
                'id'=>$project->getId(),
            ]);

        }


        return $this->render('admin/project/show.html.twig', [
            'project'=> $project,
           'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("admin/project/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $project->setDateUpdate(new \DateTime());
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('admin/project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/project/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }


}
