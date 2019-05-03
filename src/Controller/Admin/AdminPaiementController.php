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
	use App\Entity\Identification;
	use App\Repository\UserRepository;
	use App\Repository\ReservationRepository;
	use App\Repository\OffreRepository;
	use App\Repository\ClientRepository;
	use App\Repository\PaiementRepository;
	use App\Repository\IdentificationRepository;
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
		 * @var IdentificationRepository
		 */
		private $repositoryIdentification;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(OffreRepository $repoOffre,ClientRepository $repoClient, ReservationRepository $repository, UserRepository $userRepository ,IdentificationRepository   $identR, ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositoryOffre = $repoOffre;
			$this->repositoryClient = $repoClient;
			$this->repositoryIdentification = $identR;
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
		* @Route("/recep/paiement_identification/new/{id<\d+>}", name="recep.paiement.identification")
		* @return Response
		*/
		public function paiement_identification(Identification $identification):Response
		{
			if($identification->getEtat()=="Terminer"){
				$this->addFlash('erreur','Ce Séjour est déjà Terminer il ne peut plus avoir de paiement');
				return $this->redirectToRoute('recep.identification.index');
			}
			return $this->render('paiements/newPaimentIdentification.html.twig',compact('identification')); 
		}

		/**
		* @Route("/recep/paiement/add/{reservation}", name="recep.paiement.new")
		* @return Response
		*/
		public function new(Request $request,$reservation=0):Response
		{
			$prix = $this->repositoryVar->find($request->get('id'))->getPrix();
			$reste = $prix - $this->repositoryVar->find($request->get('id'))->getAvance();

			if($request->get('montant') > $prix ){
				$this->addFlash('erreur','Avance supérieure au cout total cela est impossible');
				return $this->redirectToRoute('recep.paiement.index',array('id' => $request->get('id')));
			}
			
			if($request->get('montant') > $reste ){
				$this->addFlash('erreur','Une telle avance excède sur le coup Total cela est impossible'.$reste );
				return $this->redirectToRoute('recep.paiement.identification',array('id' => $request->get('id')));
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


		/**
		* @Route("/recep/paiement/add_identification/{id}", name="recep.paiement.new_identification")
		* @return Response
		*/
		public function newIdentificationPaiement(Request $request,$id):Response
		{
			$prix = $this->repositoryIdentification->find($request->get('id'))->getCout();
			$prix = $prix + $this->repositoryIdentification->find($request->get('id'))->getCoutExtra();
			$reste = $prix - $this->repositoryIdentification->find($request->get('id'))->getAvance();

			if($request->get('montant') > $prix ){
				$this->addFlash('erreur','Avance supérieure au cout total cela est impossible');
				return $this->redirectToRoute('recep.paiement.identification',array('id' => $request->get('id')));
			}
			
			if($request->get('montant') > $reste ){
				$this->addFlash('erreur','Une telle avance excède sur le coup Total cela est impossible'.$reste );
				return $this->redirectToRoute('recep.paiement.identification',array('id' => $request->get('id')));
			}
			$paiement = new Paiement();
				$paiement->setUser($this->repositoryUser->find(2));
				$paiement->setMadeAt(new \DateTime());
				$paiement->setIdentification($this->repositoryIdentification->find($request->get('id')));
				$paiement->setMontant($request->get('montant'));
				$this->em->persist($paiement);
				$this->em->flush();
				$identification = $this->repositoryIdentification->find($request->get('id'));
				$identification->setAvance($identification->getAvance() + $paiement->getMontant());
				$identification->addPaiement($paiement);
				$this->em->flush();
				$this->addFlash('success','Avance effectuée avec succes');
				return $this->redirectToRoute('recep.identification.index');
			
		}

	}
