<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CRUDController extends AbstractController
{
    #[Route('/crud/list', name: 'c_r_u_d')]
    public function index(EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task::class)->findBy([], ["id" => "desc"]);


        return $this->render("./component/index.html.twig", ["tasks" => $tasks]);
    }

    #[Route('/create', name: 'create', methods:["POST"])]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $title = trim($request->get('title'));
        if (empty($title)) {
            return $this->redirectToRoute("c_r_u_d");
        }else {
            $task = new Task();
            $task->setTitle($title);
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute("c_r_u_d");
        }
    }

    #[Route('/update/{id}', name: 'update')]
    public function update($id, ManagerRegistry $doctrine): Response
    {   
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->getStatus());
        $entityManager->flush();
        return $this->redirectToRoute("c_r_u_d");
        // exit("crud to do update a new task with an id of: " . $id);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $entityManager->remove($task);
        $entityManager->flush();
        return $this->redirectToRoute("c_r_u_d");
    }
}
