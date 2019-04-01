<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Reservation;
	use App\Entity\Offre;
	use App\Entity\Client;
	use App\Entity\Paiement;
	use App\Repository\UserRepository;
	use App\Repository\ReservationRepository;
	use App\Repository\OffreRepository;
	use App\Repository\ClientRepository;
	use App\Repository\PaiementRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\PaiementType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminPaiementController extends AbstractController
	{
		 /**
		 * @var ReservationRepository
		 */
		private $repositoryVar;

		/**
		 * @var UserRepository
		 */
		private $repositoryUser;

		/**
		 * @var OffreRepository
		 */
		private $repositoryOffre;

		/**
		 * @var ClientRepository
		 */
		private $repositoryClient;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(OffreRepository $repoOffre,ClientRepository $repoClient, ReservationRepository $repository, UserRepository $userRepository ,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositoryOffre = $repoOffre;
			$this->repositoryClient = $repoClient;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
		}

		/**
		* @Route("/recep/reservation/details/{id<\d+>}", name="recep.reservation.details")
		* @return Response
		*/
		public function consulter(ReservationRepository $repositoryReserv,$id):Response
		{
			$reservation = $repositoryReserv->find($id);
			return $this->render('reservation/consulter.html.twig',compact('reservation')); 
		}

		/**
		* @Route("/recep/paiement/new/{id<\d+>}", name="recep.paiement.index")
		* @return Response
		*/
		public function index($id):Response
		{
			$paiement = new Paiement();
			$form = $this->createForm(PaiementType::class, $paiement);
			$paiement->setReservation($this->repositoryVar->find($id));
			return $this->render('paiements/new.html.twig',[
				'paiements'=> $paiement,
				'form'=> $form->createView()
			]); 
		}


		/**
		* @Route("/recep/paiement/add/{reservation}", name="recep.paiement.new")
		* @return Response
		*/
		public function new(Request $request,$reservation=0):Response
		{
			$prix = $this->repositoryVar->find($request->get('id'))->getPrix();
			$reste = $this->repositoryVar->find($request->get('id'))->getPrix() - $this->repositoryVar->find($request->get('id'))->getAvance();

			if($request->get('montant') > $prix ){
				$this->addFlash('erreur','Avance supérieure au cout total cela est impossible');
				return $this->redirectToRoute('recep.paiement.index',array('id' => $request->get('id')));
			}
			
			if($reste<$prix AND $request->get('montant') + $reste  > $prix ){
				$this->addFlash('erreur','Une telle avance excède sur le coup Total cela est impossible'.$reste );
				return $this->redirectToRoute('recep.paiement.index',array('id' => $request->get('id')));
			}

			$paiement = new Paiement();

				$paiement->setUser($this->repositoryUser->find(2));
				$paiement->setMadeAt(new \DateTime());
				$paiement->setReservation($this->repositoryVar->find($request->get('id')));
				$paiement->setMontant($request->get('montant'));
				$this->em->persist($paiement);
				$this->em->flush();
				$reservations = $this->repositoryVar->find($request->get('id'));
				$reservations->setAvance($reservations->getAvance() + $paiement->getMontant());
				$reservations->addPaiement($paiement);
				$this->em->flush();
				$this->addFlash('success','Avance effectuée avec succes');
				return $this->redirectToRoute('recep.reservation.details',array('id' => $paiement->getReservation()->getId()));
			
		}

	}
