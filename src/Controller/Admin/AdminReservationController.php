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
			//$this->FreeRoom();
			$this->ChangeState();
			return $this->render('reservation/index.html.twig',compact('reservations')); 
		}

		/**
		* @Route("/recep/reservation/etape2/{id<\d+>}", name="recep.reservation.etape2")
		* @return Response
		*/
		public function oldClientRervStep1(Client $client ,OffreRepository $repositoryOffre):Response
		{
			$offres = $repositoryOffre->findAll();
			return $this->render('reservation/new2.html.twig',['client'=>$client,
			'offres'=>$offres]); 
		}

		/**
		* @Route("/recep/reserv_new_client/etape2", name="recep.reservation_new_client.etape2")
		* @return Response
		*/
		public function newClientReservStep2(OffreRepository $repositoryOffre):Response
		{
			$offres = $repositoryOffre->findAll();
			return $this->render('reservation/newclient.html.twig',['offres'=>$offres]); 
		}

		/**
		* @Route("/recep/reservation/etape1", name="recep.reservation.etape1")
		* @return Response
		*/
		public function ReservStep1(ClientRepository $repositoryClient):Response
		{
			$clients = $repositoryClient->findAll();
			return $this->render('reservation/new1.html.twig',compact('clients')); 
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
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('begin_at') && null!==$request->get('end_at') && null!==$request->get('nom') && null!==$request->get('prenom') && null!==$request->get('cni') && is_numeric($request->get('telephone')) && is_numeric($request->get('prix')))
					{
						//control disponibilité
						$date = new \DateTime($request->get('begin_at'));
						$disp = $this->checkIfIsBusyReservation($request->get('offre'),$date);
						if(null != $disp )
						{
							$this->addFlash('erreur','Cette offre est déjà réservée durant cette période');
							return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
						}
						$disp = $this->checkIfIsBusyIdentification($request->get('offre'),$date);
						if($disp != null)
						{
							$this->addFlash('erreur','Cette offre ne sera occupée durant cette période');
							return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
						}
						//création des objets
						$reservation = new Reservation();
						$client = new Client();
						$client = $this->repositoryClient->findByCni($request->get('cni'));
						$offre = $this->repositoryOffre->find($request->get('offre'));

						if(is_null($client))
						{
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
						//control validité
						if($request->get('avance')==0){
							$reservation->setValide("non");	
						}else{
							$reservation->setValide("oui");
							if($offre->getQuantite()<=0){
								$offre->setQuantite(0);
							}else{
								$offre->setQuantite($offre->getQuantite()-1);	
							}
							$this->em->flush();	
						}
						//enrgistrement des données
						$reservation->setClient($client);
						$reservation->setBeginAt(new \DateTime($request->get('begin_at')));
						$reservation->setEndAt(new \DateTime($request->get('end_at')));
						$reservation->setPrix($request->get('prixtotal'));
						$reservation->setAvance($request->get('avance'));
						$reservation->setOffre($offre);
						$reservation->setUser($this->getUser());
						$reservation->setEtat("");
						$this->em->persist($reservation);
						$this->em->flush();
						$this->addFlash('success','Opération éffectuée avec succes');
						return $this->redirectToRoute('recep.reservation.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
					}
			}else{
				$this->addFlash('erreur','Formulair non reconnus');
				return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
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

		/**
		* @Route("/recep/reservation_old_client/create", name="recep.reservation_old_client.new")
		* @return Response
		*/
		public function newOldClient(Request $request):Response
		{
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('begin_at') && null!==$request->get('end_at') && null!==$request->get('nom') && null!==$request->get('prenom') && null!==$request->get('cni') && is_numeric($request->get('telephone')) && is_numeric($request->get('prix')))
					{
						//control disponibilité
						$date = new \DateTime($request->get('begin_at'));
						$disp = $this->checkIfIsBusyReservation($request->get('offre'),$date);
						if(null != $disp )
						{
							$this->addFlash('erreur','Cette offre est déjà réservée durant cette période');
							return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
						}
						$disp = $this->checkIfIsBusyIdentification($request->get('offre'),$date);
						if($disp != null)
						{
							$this->addFlash('erreur','Cette offre ne sera occupée durant cette période');
							return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
						}
						//creation des objet
						$reservation = new Reservation();
						$client = new Client();
						$client = $this->repositoryClient->findByCni($request->get('cni'));
						$offre = $this->repositoryOffre->find($request->get('offre'));
						//controle validité
						if($request->get('avance')==0){
							$reservation->setValide("non");	
						}else{
							$reservation->setValide("oui");	
							if($offre->getQuantite()<=0){
								$offre->setQuantite(0);
							}else{
								$offre->setQuantite($offre->getQuantite()-1);	
							}
							$this->em->flush();
						}
						//enregistrement des donnees
						$reservation->setClient($client);
						$reservation->setBeginAt(new \DateTime($request->get('begin_at')));
						$reservation->setEndAt(new \DateTime($request->get('end_at')));
						$reservation->setPrix($request->get('prixtotal'));
						$reservation->setAvance($request->get('avance'));
						$reservation->setOffre($this->repositoryOffre->find($request->get('offre')));
						$reservation->setUser($this->getUser());
						$reservation->setEtat("");
						$this->em->persist($reservation);
						$this->em->flush();
						$this->addFlash('success','Opération éffectuée avec succes');
						return $this->redirectToRoute('recep.reservation.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
					}
			}else{
				$this->addFlash('erreur','Formulair non reconnus');
				return $this->redirectToRoute('recep.reservation.etape2',['id'=>$request->get('offre')]);
			}
		}

		public function checkIfIsBusyReservation($id,\DateTime $debut): ?Reservation
		{
			$reservations= $this->repositoryVar->findByOffre($id);
			foreach ($reservations as $reservation) 
			{
				if($reservation->getBeginAt() <= $debut && $debut <= $reservation->getEndAt())
				{
					if($reservation->getValide()=="oui")
					{
						return $reservation;
					}
					
				}
			}
			return null;
		}

		public function checkIfIsBusyIdentification($id,\DateTime $debut): ?Reservation
		{
			$listeIdent = $this->repositoryOffre->find($id)->getIdentifications();
			foreach ($listeIdent as $identification) 
			{
				if($identification->getArrivedAt() <= $debut && $debut <= $identification->getLivedAt())
				{
					return $identification;
				}
			}
			return null;
		}


	}
