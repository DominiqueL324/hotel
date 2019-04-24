<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\Salle;
	use App\Repository\SalleRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\SalleType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminSalleController extends AbstractController
	{
		 /**
		 * @var PropertyRepository
		 */
		private $repositoryVar;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(SalleRepository $repository, ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
		}

		/**
		* @Route("/admin/salle", name="admin.salle.index")
		* @return Response
		*/
		public function index():Response
		{
			$salles = $this->repositoryVar->findAll();
			return $this->render('salle/index.html.twig',compact('salles')); 
		}

		/**
		* @Route("/admin/salle/edit/{id<\d+>}", name="admin.salle.edit")
		* @return Response
		*/
		public function edit(Salle $salle,Request $request):Response
		{
			$form = $this->createForm(SalleType::class, $salle);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->flush();
				$this->addFlash('success','Modification effectuée avec succes');
				return $this->redirectToRoute('admin.salle.index');
			}
			return $this->render('salle/edit.html.twig',[
				'salle'=> $salle,
				'form'=> $form->createView()
			]); 
		}

		/**
		* @Route("/admin/salle/create", name="admin.salle.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$salle = new Salle();
			$form = $this->createForm(SalleType::class, $salle);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->persist($salle);
				$this->em->flush();
				$this->addFlash('success','Création effectuée avec succes');
				return $this->redirectToRoute('admin.salle.index');
			}
			return $this->render('salle/new.html.twig',[
				'salle'=> $salle,
				'form'=> $form->createView()
			]);
			
		}

		/**
		* @Route("/admin/salle/delete/{id<\d+>}", name="admin.salle.delete")
		* @return Response
		*/
		public function delete(Salle $salle, Request $request):Response
		{
				$this->em->remove($salle);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('admin.salle.index');
			
		}
	}