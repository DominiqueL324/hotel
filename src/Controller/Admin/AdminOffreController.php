<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Offre;
	use App\Repository\UserRepository;
	use App\Repository\OffreRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\OffreType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminOffreController extends AbstractController
	{
		 /**
		 * @var OffretRepository
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
		
		function __construct(OffreRepository $repository, UserRepository $userRepository ,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
		}

		/**
		* @Route("/admin/offre", name="admin.offre.index")
		* @return Response
		*/
		public function index():Response
		{
			$offres = $this->repositoryVar->findAll();
			return $this->render('offres/index.html.twig',compact('offres')); 
		}

		/**
		* @Route("/admin/offre/etat", name="admin.offre.etat")
		* @return Response
		*/
		public function etat():Response
		{
			$offresFree = $this->repositoryVar->findAllFree();
			$offresBusy = $this->repositoryVar->findAllBusy();
			return $this->render('offres/etat.html.twig',[
				'offresFree'=> $offresFree,
				'offresBusy'=> $offresBusy
			]); 
		}

		/**
		* @Route("/recep/reservation/etape1", name="recep.reservation.etape1")
		* @return Response
		*/
		public function librePourReservation():Response
		{
			$offres = $this->repositoryVar->findAllFree();
			return $this->render('reservation/new1.html.twig',['offres'=> $offres,]); 
		}

		/**
		* @Route("/admin/offre/tmp", name="admin.offre.tmp")
		* @return Response
		*/
		/*public function tmp():Response
		{
			return $this->render('reservation/new.html.twig'); 
		}*/

		/**
		* @Route("/admin/offre/edit/{id<\d+>}", name="admin.offre.edit",methods="POST|GET")
		* @return Response
		*/
		public function edit(Offre $offre,Request $request):Response
		{
			$form = $this->createForm(OffreType::class, $offre);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->flush();
				$this->addFlash('success','Modification effectuée avec succes');
				return $this->redirectToRoute('admin.offre.index');
			}
			return $this->render('offres/edit.html.twig',[
				'offre'=> $offre,
				'form'=> $form->createView()
			]); 
		}

		/**
		* @Route("/admin/offre/create", name="admin.offre.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$offre = new Offre();
			$form = $this->createForm(OffreType::class, $offre);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$offre->setUser($this->repositoryUser->find(2));
				$offre->setDispo(true);
				$this->em->persist($offre);
				$this->em->flush();
				$this->addFlash('success','Création effectuée avec succes');
				return $this->redirectToRoute('admin.offre.index');
			}
			return $this->render('offres/new.html.twig',[
				'offre'=> $offre,
				'form'=> $form->createView()
			]);
			
		}

		/**
		* @Route("/admin/offre/delete/{id<\d+>}", name="admin.offre.delete")
		* @return Response
		*/
		public function delete(Offre $offre, Request $request):Response
		{
				$this->em->remove($offre);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('admin.offre.index');
			
		}
	}