<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Reservation;
	use App\Entity\Offre;
	use App\Entity\Client;
	use App\Repository\UserRepository;
	use App\Repository\ReservationRepository;
	use App\Repository\OffreRepository;
	use App\Repository\ClientRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\ReservationType;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminReservationController extends AbstractController
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
		* @Route("/recep/reservation", name="recep.reservation.index")
		* @return Response
		*/
		public function index():Response
		{
			$reservations = $this->repositoryVar->findAll();
			$this->FreeRoom();
			$this->ChangeState();
			return $this->render('reservation/index.html.twig',compact('reservations')); 
		}

		/**
		* @Route("/recep/reservation/etape2/{id<\d+>}", name="recep.reservation.etape2")
		* @return Response
		*/
		public function tmp(OffreRepository $repositoryOffre,$id):Response
		{
			$offre = $repositoryOffre->find($id);
			return $this->render('reservation/new.html.twig',compact('offre')); 
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
		* @Route("recep/reservation/edit/{id<\d+>}", name="recep.reservation.edit",methods="POST|GET")
		* @return Response
		*/
		/*public function edit(Reservation $reservation,Request $request):Response
		{
			$form = $this->createForm(ReservationType::class, $reservation);
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid())
			{
				$this->em->flush();
				$this->addFlash('success','Modification effectuée avec succes');
				return $this->redirectToRoute('recep.reservation.index');
			}
			return $this->render('reservation/edit.html.twig',[
				'reservation'=> $reservation,
				'form'=> $form->createView()
			]); 
		}*/

		/**
		* @Route("/recep/reservation/create", name="recep.reservation.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$reservation = new Reservation();
			$client = new Client();
			$client = $this->repositoryClient->findByCni($request->get('cni'));
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('nom') && null!==$request->get('prenom') && null!==$request->get('cni') && is_numeric($request->get('telephone')) && is_numeric($request->get('prix')))
					{
						if(is_null($client)){
							$client = new Client();
							$client->setCni($request->get('cni'));
							$client->setNom($request->get('nom'));
							$client->setPrenom($request->get('prenom'));
							$client->setTelephone($request->get('telephone'));
							$client->setEmail($request->get('email'));
							$client->setUser($this->repositoryUser->find(2));
							$this->em->persist($client);
							$this->em->flush();
						}
						$reservation->setClient($client);
						$reservation->setBeginAt(new \DateTime($request->get('begin_at')));
						$reservation->setEndAt(new \DateTime($request->get('end_at')));
						$reservation->setPrix($request->get('prixtotal'));
						$reservation->setAvance($request->get('avance'));
						$reservation->setOffre($this->repositoryOffre->find($request->get('id_offre')));
						$reservation->setUser($this->repositoryUser->find(2));
						$this->em->persist($reservation);
						$this->em->flush();
						$offre = $this->repositoryOffre->find($request->get('id_offre'));
						$offre->setDispo(0);
						$this->em->flush();
						return $this->redirectToRoute('recep.reservation.index');
					}else
					{
						$offre = $this->repositoryOffre->find($request->get('id_offre'));
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('id_offre')]);
					}

			}
			
		}

		/**
		* @Route("/recep/Reservation/delete/{id<\d+>}", name="recep.Reservation.delete")
		* @return Response
		*/
		/*public function delete(Reservation $Reservation, Request $request):Response
		{
				$this->em->remove($reservation);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('recep.reservation.index');
			
		}*/
		private function FreeRoom(){
			$reservations = $this->repositoryVar->findAll();
			$oday = new \DateTime();
			foreach ($reservations as $reservation) {
				if($oday>$reservation->getEndAt()  ){
					$offre = $reservation->getOffre();
					$offre->setDispo(1);
					$this->em->flush();
				}else{
					$offre = $reservation->getOffre();
					$offre->setDispo(0);
					$this->em->flush();
				}		
			}		
		}
			private function ChangeState(){
			$reservations = $this->repositoryVar->findAll();
			$oday = new \DateTime();
			foreach ($reservations as $reservation) {
				if($oday>$reservation->getEndAt()  ){
					$reservation->setEtat("Terminée");
					$this->em->flush();
				}else{
					$reservation->setEtat("En cours");
					$this->em->flush();
				}		
			}		
		}
	}
