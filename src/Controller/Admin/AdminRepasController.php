<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Repas;
	use App\Form\RepasType;
	use App\Repository\UserRepository;
	use App\Repository\RepasRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\HttpFoundation\Request;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Twig\Environment;
	use Dompdf\Dompdf;
	use Dompdf\Options;

	/**
	 * 
	 */
	class AdminRepasController extends AbstractController
	{

		/**
		 * @var ObjectManager
		 */
		private $em;

		 /**
		 * @var RepasRepository
		 */
		private $repositoryVar;
		
		function __construct(RepasRepository $repository,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
		}

		/**
		* @Route("/admin/repas", name="admin.repas.index")
		* @return Response
		*/
		public function index():Response
		{
			$repas = $this->repositoryVar->findAll();
			return $this->render('repas/index.html.twig',compact('repas')); 
		}

		/**
		* @Route("/admin/repas/create", name="admin.repas.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$repas = new Repas();
			$form = $this->createForm(RepasType::class, $repas);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$repas->setUser($this->getUser());
				$this->em->persist($repas);
				$this->em->flush();
				$this->addFlash('success','Création effectuée avec succes');
				return $this->redirectToRoute('admin.repas.index');
			}
			return $this->render('repas/new.html.twig',[
				'repas'=> $repas,
				'form'=> $form->createView()
			]);

		}		

		/**
		* @Route("/admin/repas/edit/{id}", name="admin.repas.edit")
		* @return Response
		*/
		public function edit(Request $request, repas $repas):Response
		{
			$form = $this->createForm(RepasType::class, $repas);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->flush();
				$this->addFlash('success','Modification effectuée avec succes');
				return $this->redirectToRoute('admin.repas.index');
			}
			return $this->render('repas/new.html.twig',[
				'repas'=> $repas,
				'form'=> $form->createView()
			]);
			
		}

		/**
		* @Route("/admin/repas/delete/{id<\d+>}", name="admin.repas.delete")
		* @return Response
		*/
		public function delete(Repas $repas, Request $request):Response
		{
				$this->em->remove($repas);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('admin.repas.index');
			
		}	
	}

