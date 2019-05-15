<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Identification;
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
	use  Twig\Environment;
	use Dompdf\Dompdf;
	use Dompdf\Options;

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
			$this->checkForExtraAndAdd();

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
			$offres = $this->repositoryOffre->findAllFree();
			return $this->render('identification/nouvelleSansReservation1.html.twig',['offres'=>$offres,'client'=>$client]); 
		}

		/**
		* @Route("/recep/new_client_identification", name="recep.new_client_reserv")
		* @return Response
		*/
		public function NewReservationNewClient():Response
		{
			$offres = $this->repositoryOffre->findAllFree();		
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
			$reservations = $repositoryReserv->findBy(['etat'=>'En cours']);
			return $this->render('identification/new1.html.twig',compact('reservations')); 
		}

		/**
		* @Route("/recep/identification/edit/{id}/{role}", name="recep.identification.edit")
		* @return Response
		*/
		public function editLauncher(ReservationRepository $repositoryReserv,$id,$role):Response
		{

			$identification = $this->repositoryVar->find($id);
			if($identification->getEtat() == "Terminer"){
				$this->addFlash('erreur','Ce Séjour est déjà Termineril ne peut plus être modifié');
				return $this->redirectToRoute('recep.identification.index');
			}
			$oday = new \DateTime();
			if($oday > $identification->getMadeAt() && $role != "ROLE_ADMIN"){
				$this->addFlash('erreur','Le delais de modification est excédé veuillez contacter un administrateur');
				return $this->redirectToRoute('recep.identification.index');
			}else{
				return $this->render('identification/edit.html.twig',compact('identification')); 
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
				if($this->repositoryReservation->find($request->get('idR'))->getOffre()->getQuantite()>0){

				}else{
					$this->addFlash('erreur',"cette offre n'est pas encore libre pour le moment");
					return $this->redirectToRoute('recep.new_client_reserv');
				}
				if(null!==$request->get('nationalite') && null!==$request->get('paysresidence') && null!==$request->get('profession') && null!==$request->get('datenaissance') && null!==$request->get('datecni') && null!==$request->get('venantde') && null!==$request->get('serendanta') && $request->get('sexe') !=="jondo" && "jondo"!==$request->get('reglement') && null!==$request->get('date_end') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) && null!==$request->get('lieunaissance')&& null!==$request->get('venantde')&& null!==$request->get('nuite'))
					{
						
						if($request->get('remise_ordinaire')<$request->get('remise_accordee') && $request->get('roles')!= "ROLE_ADMIN" )
						{
							$this->addFlash('erreur','Vous ne pouvez accorder une remise de ce montant contacter un administrateurpour cela');
							return $this->redirectToRoute('recep.new_client_reserv');
						}

						$client->setNationalite($request->get('nationalite'));
						$client->setPaysResidence($request->get('paysresidence'));
						$client->setProfession($request->get('profession'));
						$client->setSexe($request->get('sexe'));
						$client->setBornAt(new \DateTime($request->get('datenaissance')));
						$client->setLieuDeNaissance($request->get('lieunaissance'));
						$client->setCniMadeAt(new \DateTime($request->get('datecni')));
						$client->setType($request->get('type_client'));
						$client->setRemise($request->get('remise_ordinaire'));
						$this->em->flush();
						$identification->setReservation($this->repositoryReservation->find($request->get('idR')));
						$identification->setOffre($this->repositoryReservation->find($request->get('idR'))->getOffre());
						$identification->setAvance($this->repositoryReservation->find($request->get('idR'))->getAvance());
						$identification->setClient($client);
						$identification->setRemise($request->get('remise_accordee'));
						$identification->setUser($this->getUser());
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setCout($request->get('cout_total_normal'));
						$identification->setLivedAt(new \DateTime($request->get('date_end')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setModeReglement($request->get('reglement'));
						$identification->setImmatriculation($request->get('immatriculation'));
						$identification->setMadeAt(new \DateTime());
						$identification->setVenantDe($request->get('venantde'));
						$identification->setNombreNuite($request->get('nuite'));
						$identification->setEtat("En cours");
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
						$offre = $this->repositoryReservation->find($request->get('idR'))->getOffre();
						$this->repositoryReservation->find($request->get('idR'))->setValide("oui");
						$this->repositoryReservation->find($request->get('idR'))->setEtat("Identifiée");
						$this->em->flush();
						$offre->setQuantite($offre->getQuantite()-1);
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
		public function IdentificationNewClientCreate(Request $request):Response
		{
			$identification = new identification();
			$client = new Client();
			$debut = new \DateTime($request->get('date_arrivee'));
			$offre = $this->repositoryOffre->find($request->get('offre'));

			$reservation = $this->checkIfOffreReserveeConfirmer($request->get('offre'),$debut);
			if($reservation !=null)
			{
				$this->addFlash('erreur',"Il ne reste plus qu'une seule ".$offre->getLibelle()."  disponible et elle est déjà reservée et payée pour cette période");
				return $this->redirectToRoute('recep.new_client_reserv');
			}

			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('nom') && null!==$request->get('prenom') && null!==$request->get('cni') && is_numeric($request->get('telephone')) && null!==$request->get('nationalite') && null!==$request->get('paysresidence') && null!==$request->get('profession') && null!==$request->get('datenaissance') && null!==$request->get('datecni') && null!==$request->get('venantde') && null!==$request->get('serendanta') && $request->get('sexe') !=="jondo" && "jondo"!==$request->get('reglement') && null!==$request->get('date_depart') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) && null!==$request->get('lieunaissance') && null!==$request->get('venantde')&& null!==$request->get('nuite'))
					{

						if($request->get('remise_ordinaire')<$request->get('remise_accordee') && $request->get('roles')!= "ROLE_ADMIN" )
						{
							$this->addFlash('erreur','Vous ne pouvez accorder une remise de ce montant contacter un administrateurpour cela');
							return $this->redirectToRoute('recep.new_client_reserv');
						}

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
							$client->setUser($this->repositoryUser->find($this->getUser()));
							$client->setType($request->get('type_client'));
							$client->setRemise($request->get('remise_ordinaire'));
							$this->em->persist($client);
							$this->em->flush();	
						}else{
							$this->addFlash('erreur','Un client avec cette CNI/Passeport existe déjà');
							return $this->redirectToRoute('recep.new_client_reserv');
						}
						

						$identification->setClient($client);
						$identification->setCout($request->get('cout_total_normal'));
						$identification->setRemise($request->get('remise_accordee'));
						$identification->setUser($this->repositoryUser->find(2));
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setLivedAt(new \DateTime($request->get('date_depart')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setModeReglement($request->get('reglement'));
						$identification->setImmatriculation($request->get('immatriculation'));
						$identification->setMadeAt(new \DateTime());
						$identification->setVenantDe($request->get('venantde'));
						$identification->setNombreNuite($request->get('nuite'));
						$identification->setAvance(0);
						$identification->setEtat("En cours");
						$identification->setOffre($this->repositoryOffre->find($request->get('offre')));
						$Maxid = $this->repositoryVar->getMaxId();
						$date = new \DateTime();
						if(is_null($Maxid)){
							$Maxid = 1;
							$result1 ="CICM".date("m")."".$Maxid.date("y");
						}else{
							$result1 ="CICM".date("m")."".$Maxid['id']."".date("y");
						}
						$offre = $this->repositoryOffre->find($request->get('offre'));
						$identification->setNumeroIdentification($result1);
						$this->em->persist($identification);
						$this->em->flush();
						$offre->setQuantite($offre->getQuantite()-1);
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
			$offre = $this->repositoryOffre->find($request->get('offre'));
			$debut = new \DateTime($request->get('date_arrivee'));

			$reservation = $this->checkIfOffreReserveeConfirmer($request->get('offre'),$debut);
			if($reservation !=null)
			{
				$this->addFlash('erreur',"Il ne reste plus qu'une seule ".$offre->getLibelle()."  disponible et elle est déjà reservée et payée pour cette période");
				return $this->redirectToRoute('recep.noreservationidentification.etape2',['id'=>$client->getId()]);
			}

			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if( null!==$request->get('profession') &&  null!==$request->get('datecni') && null!==$request->get('venantde') && null!==$request->get('serendanta') && $request->get('sexe') !=="jondo" && "jondo"!==$request->get('reglement') && null!==$request->get('date_end') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) )
					{
						$offre = $this->repositoryOffre->find($request->get('offre'));
						if($request->get('remise_ordinaire')<$request->get('remise_accordee') && $request->get('roles')!= "ROLE_ADMIN" )
						{
							$this->addFlash('erreur','Vous ne pouvez accorder une remise de ce montant contacter un administrateurpour cela');
							return $this->redirectToRoute('recep.new_client_reserv');
						}
						
						$identification->setClient($client);
						$identification->setRemise($request->get('remise_accordee'));
						$identification->setCout($request->get('cout_total_normal'));
						$identification->setUser($this->getUser());
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setLivedAt(new \DateTime($request->get('date_end')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setAvance(0);
						$identification->setModeReglement($request->get('reglement'));
						$identification->setImmatriculation($request->get('immatriculation'));
						$identification->setMadeAt(new \DateTime());
						$identification->setVenantDe($request->get('venantde'));
						$identification->setNombreNuite($request->get('nuite'));
						$identification->setEtat("En cours");
						$identification->setOffre($offre);
						$Maxid = $this->repositoryVar->getMaxId();
						$identification->setMadeAt(new \DateTime());

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
						$offre->setQuantite($offre->getQuantite()-1);
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.identification.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.noreservationidentification.etape2',['id'=>$client->getId()]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.noreservationidentification.etape2',['id'=>$client->getId()]);
			}
		}

		public function UpdatePriceAndAvance()
		{
			/*$listeIdent = $this->repositoryVar->findAll();
			foreach ($listeIdent as $identification) 
			{
				if($identification->getEtat()!="Terminer")
				{
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
				
			}*/
		}

		/**
		* @Route("/recep/identification/consult/{id<\d+>}", name="recep.identification.consult")
		* @return Response
		*/
		public function Consult(Identification $identification):Response
		{
			return $this->render('identification/consult.html.twig',compact('identification'));
		}

		public function checkForExtraAndAdd()
		{
			$listeIdent = $this->repositoryVar->findAll();
			$oday = new \DateTime();
			foreach ($listeIdent as $identification) 
			{
				if($identification->getEtat()!="Terminer"){
					if($oday>$identification->getLivedAt()){
						$difference = $identification->getLivedAt()->diff(new \DateTime(),true)->d;
						$identification->setCoutExtra($difference*$identification->getOffre()->getPrix());
						$this->em->flush();
					}else{
						$identification->setCoutExtra(0);
						$this->em->flush();
					}
				}
				
				
			}
		}

		/**
		* @Route("/recep/identification/factrurer/{id<\d+>}", name="recep.identification.facturer")
		* @return Response
		*/
		public function facturer(Identification $identification):Response
		{
			if($identification->getEtat()=="Terminer"){
				$this->addFlash('erreur','Identification déjà facturer');
				return $this->redirectToRoute('recep.identification.consult',['id'=>$identification->getId()]);
			}
			$identification->setEtat("Terminer");
			$offre = $identification->getOffre();
			$offre = $this->repositoryOffre->find($offre->getId());
			if($offre->getQuantite()<$offre->getTotal())
			{
				$offre->setQuantite($offre->getQuantite()+1);
				$this->em->flush();
			}
			$this->em->flush();
			$pdfOptions = new Options();
	        $pdfOptions->set('defaultFont', 'Helvetica');
	        $pdfOptions->set('isRemoteEnabled',true);
	        // Instantiate Dompdf with our options
	        $dompdf = new Dompdf($pdfOptions);
	        // Retrieve the HTML generated in our twig file
	        $html = $this->renderView('pdf/factureIdentification.html.twig',['identification'=>$identification]);
	        // Load HTML to Dompdf
	        $dompdf->loadHtml($html);
	        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
	        $dompdf->setPaper('A4', 'portrait');
	        // Render the HTML as PDF
	        $dompdf->render();
	        // Output the generated PDF to Browser (force download)
	        $dompdf->stream("facture.pdf", [
	            "Attachment" => false
	        ]);
			//return $this->redirectToRoute('recep.identification.index');
			//génération de la facture
		}

		/**
		* @Route("/recep/identification/imprimer/{id<\d+>}", name="recep.identification.imprimer")
		* @return Response
		*/
		public function imprimer(Identification $identification):Response
		{
			$pdfOptions = new Options();
	        $pdfOptions->set('defaultFont', 'Helvetica');
	        $pdfOptions->set('isRemoteEnabled',true);
	        // Instantiate Dompdf with our options
	        $dompdf = new Dompdf($pdfOptions);
	        // Retrieve the HTML generated in our twig file
	        $html = $this->renderView('pdf/factureIdentification.html.twig',['identification'=>$identification]);
	        // Load HTML to Dompdf
	        $dompdf->loadHtml($html);
	        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
	        $dompdf->setPaper('A4', 'portrait');
	        // Render the HTML as PDF
	        $dompdf->render();
	        // Output the generated PDF to Browser (force download)
	        $dompdf->stream("facture.pdf", [
	            "Attachment" => false
	        ]);
			
			//génération de la facture
		}		

		public function checkIfOffreReserveeConfirmer($id,\DateTime $date)
		{
			$reservations= $this->repositoryReservation->findBy(['offre'=>$id]);
			$offre = $this->repositoryOffre->find($id);
			foreach ($reservations as $reservation) {
				if($reservation->getBeginAt() <= $date && $date<= $reservation->getEndAt()){
					if($reservation->getAvance()>0 and $offre->getQuantite()==1){
						return $reservation;
					}
				}
			}
			return null;
		}



		/**
		* @Route("/recep/identification/edits/{id}", name="recep.identification.edits")
		* @return Response
		*/
		public function edit(Request $request, Identification $identification):Response
		{
			if($this->isCsrfTokenValid('edit',$request->get('_token')))
			{
				
				if(null!==$request->get('venantde') && null!==$request->get('serendanta') && null!==$request->get('date_depart') && null!==$request->get('date_arrivee') && is_numeric($request->get('nuite')) &&null!==$request->get('venantde'))
					{
						
						if($request->get('remise_ordinaire')<$request->get('remise_accordee') && $request->get('roles')!= "ROLE_ADMIN" )
						{
							$this->addFlash('erreur','Vous ne pouvez accorder une remise de ce montant contacter un administrateurpour cela');
							return $this->redirectToRoute('recep.new_client_reserv');
						}
						$identification->setRemise($request->get('remise_accordee'));
						$identification->setCout($request->get('cout_total_normal'));
						$identification->setUser($this->getUser());
						$identification->setArrivedAt(new \DateTime($request->get('date_arrivee')));
						$identification->setLivedAt(new \DateTime($request->get('date_depart')));
						$identification->setNombrePersonne($request->get('nombrepersonne'));
						$identification->setSeRendantA($request->get('serendanta'));
						$identification->setModeReglement($request->get('reglement'));
						$identification->setMadeAt(new \DateTime());
						$identification->setVenantDe($request->get('venantde'));
						$identification->setNombreNuite($request->get('nuite'));
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.identification.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.identification.edit',['id'=>$identification->getId(),
					 'role'=>'ROLE_ADMIN']);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.identification.edit',['id'=>$identification->getId(),
					 'role'=>'ROLE_ADMIN']);
			}
		}	
	}

