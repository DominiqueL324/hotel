<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Client;
	use App\Repository\UserRepository;
	use App\Repository\ClientRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\ClientType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminClientController extends AbstractController
	{
		 /**
		 * @var ClientRepository
		 */
		private $repositoryVar;

		/**
		 * @var UserRepository
		 */
		private $repositoryUser;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(ClientRepository $repository, UserRepository $userRepository ,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
		}

		/**
		* @Route("/admin/client", name="admin.client.index")
		* @return Response
		*/
		public function index():Response
		{
			$user = $this->getUser();
			$clients = $this->repositoryVar->findAll();
			return $this->render('clients/index.html.twig',['clients'=>$clients,'user'=>$user]); 
		}

		/**
		* @Route("/admin/client/edit/{id<\d+>}", name="admin.client.edit",methods="POST|GET")
		* @return Response
		*/
		public function edit(Client $client,Request $request):Response
		{
			$form = $this->createForm(ClientType::class, $client);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				/*$client1 = $this->repositoryVar->findOneBy(['cni'=>$client->getCni()]);
				if($client1!=null and ){
					$this->addFlash('erreur','Un client avec cette cni existe dejà');
					return $this->render('clients/new.html.twig',[
												'client'=> $client,
												'form'=> $form->createView(),
												'user'=>$user
											]);
				}*/
				$this->em->flush();
				$this->addFlash('success','Modification effectuée avec succes');
				return $this->redirectToRoute('admin.client.index');
			}
			$user = $this->getUser();
			return $this->render('clients/edit.html.twig',[
				'client'=> $client,
				'form'=> $form->createView(),
				'user'=>$user
			]); 
		}

		/**
		* @Route("/admin/client/create", name="admin.client.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$user = $this->getUser();
			$client = new Client();
			$form = $this->createForm(ClientType::class, $client);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$client1 = $this->repositoryVar->findOneBy(['cni'=>$client->getCni()]);
				if($client1!=null){
					$this->addFlash('erreur','Un client avec cette cni existe dejà');
					return $this->render('clients/new.html.twig',[
												'client'=> $client,
												'form'=> $form->createView(),
												'user'=>$user
											]);
				}
				$client->setUser($this->getUser());
				$this->em->persist($client);
				$this->em->flush();
				$this->addFlash('success','Création effectuée avec succes');
				return $this->redirectToRoute('admin.client.index');
			}
			return $this->render('clients/new.html.twig',[
				'client'=> $client,
				'form'=> $form->createView(),
				'user'=>$user
			]);
			
		}

		/**
		* @Route("/admin/client/delete/{id<\d+>}", name="admin.client.delete")
		* @return Response
		*/
		public function delete(Client $client, Request $request):Response
		{
				$this->em->remove($client);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('admin.client.index');
			
		}
	}