<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Project;
use App\Entity\Tache;
use App\Entity\User;
use App\Form\TacheType;
use App\Repository\ProjectRepository;
use App\Repository\TacheRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tache")
 */
class TacheController extends AbstractController
{
    /**
     * @Route("/{id}", name="tache_index", methods={"GET"})
     */
    public function index(TacheRepository $tacheRepository, Project $project): Response
    {
        $listTaches = $tacheRepository->findBy(['project'=> $project->getId()]);
        
        return $this->render('admin/tache/index.html.twig', [
            'projectId' => $project->getId(),
            'taches' => $listTaches,
        ]);
    }

    /**
     * @Route("/new/{id}", name="tache_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserRepository $userRepository, ProjectRepository $projectRepository, Project $project): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                // On copie le fichier dans le dossier uploads
                $image->move(
                $this->getParameter('upload_directory'),
                $fichier);
                // On crée l'image dans la base de données
                $img = new Image();
                $img->setNom($fichier);
                $tache->addImage($img);
                $tache->setProject($project);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tache);
                $entityManager->flush();

                
            }
        }
        return $this->render('admin/tache/new.html.twig', [
            'id'=> $project->getId(),
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tache_show", methods={"GET"})
     */
    public function show(Tache $tache): Response
    {
        return $this->render('admin/tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tache_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tache $tache): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tache_index');
        }

        return $this->render('admin/tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tache_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tache $tache): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tache_index');
    }
}
