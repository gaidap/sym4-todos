<?php
    
    namespace App\Controller;
    
    use App\Entity\Task;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class ToDoListController extends AbstractController {
        /**
         * @Route("/", name="to_do_list")
         */
        public function index(): Response {
            $tasks = $this->getDoctrine()->getRepository(Task::class)->findBy([], ['id' => 'DESC']);
            return $this->render('index.html.twig', [
                'tasks' => $tasks,
            ]);
        }
        
        /**
         * @Route("/create", name="create_task", methods={"POST"})
         */
        public function create(Request $request): Response {
            $title = trim($request->request->get('title'));
            
            if (empty($title)) {
                return $this->redirectToRoute('to_do_list');
            }
            
            $this->saveTask($title);
            return $this->redirectToRoute('to_do_list');
        }
        
        /**
         * @Route("/delete/{id}", name="delete_task")
         */
        public function delete(Task $id): Response {
            $em = $this->getDoctrine()->getManager();
            $em->remove($id);
            $em->flush();
            return $this->redirectToRoute('to_do_list');
        }
        
        /**
         * @Route("/switch-status/{id}", name="switch_status")
         */
        public function switchStatus($id): Response {
            if (!is_numeric($id)) {
                return $this->redirectToRoute('to_do_list');
            }
            $this->toggleStatus($id);
            return $this->redirectToRoute('to_do_list');
        }
        
        /**
         * @param string $title
         */
        private function saveTask(string $title): void {
            $em = $this->getDoctrine()->getManager();
            $task = new Task();
            $task->setTitle($title);
            $em->persist($task);
            $em->flush();
        }
        
        /**
         * @param int $id
         */
        private function toggleStatus(int $id): void {
            $em = $this->getDoctrine()->getManager();
            $task = $em->getRepository(Task::class)->find($id);
            $task->setStatus(!$task->getStatus());
            $em->flush();
        }
    }
