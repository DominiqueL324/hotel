<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Repository\UserRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\UserType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminUserController extends AbstractController
	{
		 /**
		 * @var PropertyRepository
		 */
		private $repositoryVar;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(UserRepository $repository, ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
		}

		/**
		* @Route("/admin/user", name="admin.user.index")
		* @return Response
		*/
		public function index():Response
		{
			$users = $this->repositoryVar->findAll();
			return $this->render('users/index.html.twig',compact('users')); 
		}

		/**
		* @Route("admin/user/edit/{id<\d+>}", name="admin.user.edit")
		* @return Response
		*/
		public function edit(User $user,Request $request):Response
		{
			$form = $this->createForm(UserType::class, $user);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->flush();
				$this->addFlash('success','Modification effectuée avec succes');
				return $this->redirectToRoute('admin.user.index');
			}
			return $this->render('users/edit.html.twig',[
				'user'=> $user,
				'form'=> $form->createView()
			]); 
		}

		/**
		* @Route("/admin/user/create", name="admin.user.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$user = new User();
			$form = $this->createForm(UserType::class, $user);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->persist($user);
				$this->em->flush();
				$this->addFlash('success','Création effectuée avec succes');
				return $this->redirectToRoute('admin.user.index');
			}
			return $this->render('users/new.html.twig',[
				'user'=> $user,
				'form'=> $form->createView()
			]);
			
		}

		/**
		* @Route("/admin/user/delete/{id<\d+>}", name="admin.user.delete")
		* @return Response
		*/
		public function delete(User $user, Request $request):Response
		{
				$this->em->remove($user);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('admin.user.index');
			
		}
	}