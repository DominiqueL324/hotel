<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\identification;
	use App\Entity\Offre;
	use App\Entity\Client;
	use App\Repository\UserRepository;
	use App\Repository\ReservationRepository;
	use App\Repository\OffreRepository;
	use App\Repository\ClientRepository;
	use App\Repository\IdentificationRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use App\Form\identificationType;
	use Symfony\Component\HttpFoundation\Request;
	use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

	/**
	 * 
	 */
	class AdminIdentificationController extends AbstractController
	{
		/**
		 * @var IdentificationRepository
		 */
		private $repositoryVar;
		 /**
		 * @var ReservationRepository
		 */
		private $repositoryReservation;

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
		
		function __construct(OffreRepository $repoOffre,ClientRepository $repoClient,ReservationRepository $repoReserv, IdentificationRepository $repository, UserRepository $userRepository ,ObjectManager $em)
		{
			$this->repositoryReservation = $repoReserv;
			$this->repositoryVar = $repository;
			$this->repositoryOffre = $repoOffre;
			$this->repositoryClient = $repoClient;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
		}

		/**
		* @Route("/recep/identification", name="recep.identification.index")
		* @return Response
		*/
		public function index():Response
		{
			$this->UpdatePriceAndAvance();
			$identifications = $this->repositoryVar->findAll();
			return $this->render('Identification/index.html.twig',compact('identifications')); 
		}


		/**
		* @Route("/recep/identification_no_reservation/etape1", name="recep.noreservationidentification.etape1")
		* @return Response
		*/
		public function tmpN1():Response
		{
			$clients = $this->repositoryClient->findAll();
			return $this->render('identification/nouvelleSansReservation.html.twig',compact('clients')); 
		}

		/**
		* @Route("/recep/identification_no_reservation/etape2/{id<\d+>}", name="recep.noreservationidentification.etape2")
		* @return Response
		*/
		public function tmpN2($id):Response
		{
			$client = $this->repositoryClient->find($id);
			$offres = $this->repositoryOffre->findBy(['dispo'=>true]);
			return $this->render('identification/nouvelleSansReservation1.html.twig',['offres'=>$offres,'client'=>$client]); 
		}

		/**
		* @Route("/recep/new_client_identification", name="recep.new_client_reserv")
		* @return Response
		*/
		public function NewReservationNewClient():Response
		{
			$offres = $this->repositoryOffre->findBy(['dispo'=>true]);
			return $this->render('identification/NewReservationNewClient.html.twig',['offres'=>$offres]); 
		}

		/**
		* @Route("/recep/identification/etape2/{id<\d+>}", name="recep.identification.etape2")
		* @return Response
		*/
		public function tmp2(ReservationRepository $repositoryReserv,$id):Response
		{
			$reservation = $repositoryReserv->find($id);
			return $this->render('identification/new2.html.twig',compact('reservation')); 
		}

		/**
		* @Route("/recep/identification/etape1", name="recep.identification.etape1")
		* @return Response
		*/
		public function tmp1(ReservationRepository $repositoryReserv):Response
		{
			$reservations = $repositoryReserv->findBy(['valide'=>'non']);
			return $this->render('identification/new1.html.twig',compact('reservations')); 
		}

		/**
		* @Route("/recep/identification/edit/{id}", name="recep.identification.edit")
		* @return Response
		*/
		public function editLauncher(ReservationRepository $repositoryReserv,$id):Response
		{

			$identification = $this->repositoryVar->find($id);
			$oday = new \DateTime();
			if($oday > $identification->getMadeAt()){
				$this->addFlash('erreur','Le delais de modification est excédé veuillez contacter un administrateur');
				return $this->redirectToRoute('recep.identification.index');
			}else{

			}
			return $this->render('identification/index.html.twig'); 
		}

		/**
		* @Route("/recep/identification/create", name="recep.identification.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			$identification = new identification();
			$client = new Client();
			$client = $this->repositoryClient->find($request->get('id'));
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('nationalite') && null!==$request->get('paysresidence') && null!==$request->get('profession') && null!==$request->get('datenaissance') && null!==$request->get('datecni') && null!==$request->get('venantde') && null!==$request->get('serendanta') && $request->get('sexe') !=="jondo" && "jondo"!==$request->get('reglement') && null!==$request->get('date_depart') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) && null!==$request->get('lieunaissance'))
					{

						$client->setNationalite($request->get('nationalite'));
						$client->setPaysResidence($request->get('paysresidence'));
						$client->setProfession($request->get('profession'));
						$client->setSexe($request->get('sexe'));
						$client->setBornAt(new \DateTime($request->get('datenaissance')));
						$client->setLieuDeNaissance($request->get('lieunaissance'));
						$client->setCniMadeAt(new \DateTime($request->get('datecni')));
						$this->em->flush();
						$identification->setReservation($this->repositoryReservation->find($request->get('idR')));
						$identification->setOffre($this->repositoryReservation->find($request->get('idR'))->getOffre());
						$identification->setClient($client);
						$identification->setUser($this->repositoryUser->find(2));
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setLivedAt(new \DateTime($request->get('date_depart')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setModeReglement($request->get('reglement'));
						$identification->setImmatriculation($request->get('immatriculation'));
						$identification->setMadeAt(new \DateTime());
						$Maxid = $this->repositoryVar->getMaxId();$identification->setMadeAt(new \DateTime());
						$date = new \DateTime();
						if(is_null($Maxid)){
							$Maxid = 1;
							$result1 ="CICM".date("m")."".$Maxid.date("y");
						}else{
							$result1 ="CICM".date("m")."".$Maxid['id']."".date("y");
						}
						$identification->setNumeroIdentification($result1);
						$this->em->persist($identification);
						$this->em->flush();

						$this->repositoryReservation->find($request->get('idR'))->setValide("oui");
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.identification.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.identification.etape2',['id'=>$request->get('idR')]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.identification.etape2',['id'=>$request->get('idR')]);
			}
		}

		/**
		* @Route("/recep/identification/delete/{id<\d+>}", name="recep.identification.delete")
		* @return Response
		*/
		/*public function delete(identification $identification, Request $request):Response
		{
				$this->em->remove($identification);
				$this->em->flush();
				$this->addFlash('success','Supression effectuée avec succes');
				return $this->redirectToRoute('recep.identification.index');
			
		}*/


		/**
		* @Route("/recep/identification_new_client/create", name="recep.new_identification_new_client")
		* @return Response
		*/
		public function ReservationNewClientCreate(Request $request):Response
		{
			$identification = new identification();
			$client = new Client();
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('nom') && null!==$request->get('prenom') && null!==$request->get('cni') && is_numeric($request->get('telephone')) && null!==$request->get('nationalite') && null!==$request->get('paysresidence') && null!==$request->get('profession') && null!==$request->get('datenaissance') && null!==$request->get('datecni') && null!==$request->get('venantde') && null!==$request->get('serendanta') && $request->get('sexe') !=="jondo" && "jondo"!==$request->get('reglement') && null!==$request->get('date_depart') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) && null!==$request->get('lieunaissance'))
					{
						$client = $this->repositoryClient->findByCni($request->get('cni'));
						if(is_null($client))
						{
							$client = new Client();
							$client->setNom($request->get('nom'));
							$client->setPrenom($request->get('prenom'));
							$client->setTelephone($request->get('telephone'));
							$client->setCni($request->get('cni'));
							$client->setNationalite($request->get('nationalite'));
							$client->setPaysResidence($request->get('paysresidence'));
							$client->setProfession($request->get('profession'));
							$client->setSexe($request->get('sexe'));
							$client->setBornAt(new \DateTime($request->get('datenaissance')));
							$client->setLieuDeNaissance($request->get('lieunaissance'));
							$client->setCniMadeAt(new \DateTime($request->get('datecni')));
							$client->setUser($this->repositoryUser->find(2));
							$this->em->persist($client);
							$this->em->flush();	
						}else{
							$this->addFlash('erreur','Un client avec cette CNI/Passeport existe déjà');
							return $this->redirectToRoute('recep.new_client_reserv');
						}
						

						$identification->setClient($client);
						$identification->setCout(0.0);
						$identification->setUser($this->repositoryUser->find(2));
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setLivedAt(new \DateTime($request->get('date_depart')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setModeReglement($request->get('reglement'));
						$identification->setImmatriculation($request->get('immatriculation'));
						$identification->setMadeAt(new \DateTime());
						$identification->setOffre($this->repositoryOffre->find($request->get('offre')));
						$Maxid = $this->repositoryVar->getMaxId();
						$date = new \DateTime();
						if(is_null($Maxid)){
							$Maxid = 1;
							$result1 ="CICM".date("m")."".$Maxid.date("y");
						}else{
							$result1 ="CICM".date("m")."".$Maxid['id']."".date("y");
						}
						$identification->setNumeroIdentification($result1);
						$this->em->persist($identification);
						$this->em->flush();
						$this->repositoryOffre->find($request->get('offre'))->setDispo(false);
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.identification.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.new_client_reserv');
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.new_client_reserv');
			}
		}

		/**
		* @Route("/recep/identification_no_reserv_old_client/create", name="recep.identification_no_reserv_old_clien.new")
		* @return Response
		*/
		public function newIdentificationOldClientNoReserv(Request $request):Response
		{
			$identification = new identification();
			$client = new Client();
			$client = $this->repositoryClient->find($request->get('id'));
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('nationalite') && null!==$request->get('paysresidence') && null!==$request->get('profession') && null!==$request->get('datenaissance') && null!==$request->get('datecni') && null!==$request->get('venantde') && null!==$request->get('serendanta') && $request->get('sexe') !=="jondo" && "jondo"!==$request->get('reglement') && null!==$request->get('date_depart') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) && null!==$request->get('lieunaissance'))
					{

						$client->setNationalite($request->get('nationalite'));
						$client->setPaysResidence($request->get('paysresidence'));
						$client->setProfession($request->get('profession'));
						$client->setSexe($request->get('sexe'));
						$client->setBornAt(new \DateTime($request->get('datenaissance')));
						$client->setLieuDeNaissance($request->get('lieunaissance'));
						$client->setCniMadeAt(new \DateTime($request->get('datecni')));
						$this->em->flush();

						$identification->setClient($client);
						$identification->setUser($this->repositoryUser->find(2));
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setLivedAt(new \DateTime($request->get('date_depart')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setModeReglement($request->get('reglement'));
						$identification->setImmatriculation($request->get('immatriculation'));
						$identification->setMadeAt(new \DateTime());
						$identification->setOffre($this->repositoryOffre->find($request->get('offre')));
						$Maxid = $this->repositoryVar->getMaxId();$identification->setMadeAt(new \DateTime());

						$date = new \DateTime();
						if(is_null($Maxid)){
							$Maxid = 1;
							$result1 ="CICM".date("m")."".$Maxid.date("y");
						}else{
							$result1 ="CICM".date("m")."".$Maxid['id']."".date("y");
						}
						$identification->setNumeroIdentification($result1);
						$this->em->persist($identification);
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.identification.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.recep.noreservationidentification.etape2',['id'=>$client->getId()]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.recep.noreservationidentification.etape2',['id'=>$client->getId()]);
			}
		}

		public function UpdatePriceAndAvance(){
			$listeIdent = $this->repositoryVar->findAll();
			foreach ($listeIdent as $identification) {
				$avance = 0;
				$debut = $identification->getArrivedAt();
				$oday = new \DateTime();
				$difference = $debut->diff($oday)->d;
				$cout = ($difference+1) * $identification->getOffre()->getPrix();
				$identification->setCout($cout);
				$listeAvance = $identification->getPaiements();
				if($listeAvance->isEmpty()){
					$identification->setAvance(0);	
				}else{
					foreach ($listeAvance as  $value) {
						$avance = $avance + $value->getMontant();
					}
					$identification->setAvance($avance);
				}
				$this->em->flush();
			}
		}
		
	}
