<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Location;
	use App\Entity\Salle;
	use App\Entity\Client;
	use App\Repository\UserRepository;
	use App\Repository\LocationRepository;
	use App\Repository\SalleRepository;
	use App\Repository\ClientRepository;
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
	class AdminLocationController extends AbstractController
	{
		/**
		 * @var LocationRepository
		 */
		private $repositoryVar;
		 /**
		 * @var SalleRepository
		 */
		private $repositorySalle;

		/**
		 * @var UserRepository
		 */
		private $repositoryUser;

		/**
		 * @var ClientRepository
		 */
		private $repositoryClient;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(SalleRepository $repoSalle,ClientRepository $repoClient, LocationRepository $repository, UserRepository $userRepository ,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositorySalle= $repoSalle;
			$this->repositoryClient = $repoClient;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
		}

		/**
		* @Route("/recep/location", name="recep.location.index")
		* @return Response
		*/
		public function index():Response
		{
			$locations = $this->repositoryVar->findAll();
			return $this->render('location/index.html.twig',compact('locations')); 
		}


		/**
		* @Route("/recep/location/etape1", name="recep.location.etape1")
		* @return Response
		*/
		public function etape1New():Response
		{
			$clients = $this->repositoryClient->findAll();
			return $this->render('location/new1.html.twig',compact('clients')); 
		}

		/**
		* @Route("/recep/location/etape2/{id<\d+>}", name="recep.location.etape2")
		* @return Response
		*/
		public function etape2New(Client $client):Response
		{
			$salles = $this->repositorySalle->findAll();
			return $this->render('location/new2.html.twig',['salles'=>$salles,'client'=>$client]); 
		}

		/**
		* @Route("/recep/location_newclient", name="recep.new_client_location")
		* @return Response
		*/
		public function LocationNewClient():Response
		{
			$salles = $this->repositorySalle->findAll();
			return $this->render('location/newclient.html.twig',['salles'=>$salles]); 
		}


		/**
		* @Route("/recep/location/create", name="recep.location.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				
				if(null!==$request->get('date_debut') && null!==$request->get('date_fin') && null!==$request->get('motif') && null!==$request->get('caution') &&  is_numeric($request->get('cout_normal') ))
					{
						$debut = new \DateTime($request->get('date_debut'));
						$result = $this->checkIfIsBusy($request->get('salleliste'),$debut);
						if($result != null){
							$this->addFlash('erreur',"cette salle est pas occupée  pour le moment");
							return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
						}
						if($request->get('remise')>10000&& $request->get('roles')!= "ROLE_ADMIN" )
						{
							$this->addFlash('erreur','Vous ne pouvez accorder une remise de ce montant contacter un administrateurpour cela');
							return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
						}
						$client = new Client();
						$client = $this->repositoryClient->find($request->get('id'));
						$salle = new Salle();
						$salle = $this->repositorySalle->find($request->get('salleliste'));
						$fin = new \DateTime($request->get('date_fin'));
						$day = $debut->diff($fin);
						$location = new location();

						$Maxid = $this->repositoryVar->getMaxId();
						$date = new \DateTime();
						if(is_null($Maxid)){
							$Maxid = 1;
							$result1 ="CICM".date("m")."".$Maxid.date("y");
						}else{
							$result1 ="CICM".date("m")."".$Maxid['id']."".date("y");
						}

						$location->setNumero($result1);
						$location->setRemise($request->get('remise'));
						$location->setBeginAt(new \DateTime($request->get('date_debut')));
						$location->setEndAt(new \DateTime($request->get('date_fin')));
						$location->setEtat("En cours");
						$location->setCaution($request->get('caution'));
						$location->setSalle($salle);
						$location->setCoutTotal(($day->d+1) * $salle->getPrix());
						$location->setCLient($client);
						$location->setMotif($request->get('motif'));
						$location->setUser($this->getUser());
						$this->em->persist($location);
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.location.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
			}
		}

		/**
		* @Route("/recep/location_new_client/create", name="recep.location_new_client.new")
		* @return Response
		*/
		public function newClientLocation(Request $request):Response
		{
			
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				$result = $this->checkIfIsBusy($request->get('id'));
				if($result !=null){
					$this->addFlash('erreur',"cette salle est pas occupée  pour le moment");
					return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
				}
				if(null!==$request->get('date_debut') && null!==$request->get('date_fin') && null!==$request->get('motif') && null!==$request->get('caution') &&  is_numeric($request->get('cout_normal')) && null!==$request->get('nom') && null!==$request->get('prenom') && null!==$request->get('cni') && is_numeric($request->get('telephone')) && null!==$request->get('nationalite') && null!==$request->get('paysresidence') && null!==$request->get('profession') && null!==$request->get('datenaissance') && null!==$request->get('datecni') && $request->get('sexe') !=="jondo"  && null!==$request->get('lieunaissance')  )
					{
						$debut = new \DateTime($request->get('date_debut'));
						$result = $this->checkIfIsBusy($request->get('salleliste'),$debut);
						if($result != null){
							$this->addFlash('erreur',"cette salle est pas occupée  pour le moment");
							return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
						}
						if($request->get('remise')>10000&& $request->get('roles')!= "ROLE_ADMIN" )
						{
							$this->addFlash('erreur','Vous ne pouvez accorder une remise de ce montant contacter un administrateurpour cela');
							return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
						}

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
			
							$salle = new Salle();
							$salle = $this->repositorySalle->find($request->get('salleliste'));
							$debut = new \DateTime($request->get('date_debut'));
							$fin = new \DateTime($request->get('date_fin'));
							$day = $debut->diff($fin);
							$location = new location();

						$Maxid = $this->repositoryVar->getMaxId();
						$date = new \DateTime();
						if(is_null($Maxid)){
							$Maxid = 1;
							$result1 ="CICM".date("m")."".$Maxid.date("y");
						}else{
							$result1 ="CICM".date("m")."".$Maxid['id']."".date("y");
						}

						$location->setNumero($result1);
						$location->setRemise($request->get('remise'));
						$location->setEndAt(new \DateTime($request->get('date_fin')));
						$location->setEtat("En cours");
						$location->setCaution($request->get('caution'));
						$location->setSalle($salle);
						$location->setCoutTotal(($day->d+1) * $salle->getPrix());
						$location->setCLient($client);
						$location->setMotif($request->get('motif'));
						$location->setUser($this->getUser());
						$this->em->persist($location);
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.location.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.location.etape2',['id'=>$request->get('id')]);
			}
		}

		/**
		* @Route("/recep/location/consult/{id<\d+>}", name="recep.location.consult")
		* @return Response
		*/
		public function Consult(Location $location):Response
		{
			return $this->render('location/consult.html.twig',compact('location'));
		}



		/**
		* @Route("/recep/location/factrurer/{id<\d+>}", name="recep.location.facturer")
		* @return Response
		*/
		public function facturer(location $location):Response
		{

			$location->setEtat("Terminer");
			$this->em->flush();
			$pdfOptions = new Options();
	        $pdfOptions->set('defaultFont', 'Helvetica');
	        $pdfOptions->set('isRemoteEnabled',true);
	        // Instantiate Dompdf with our options
	        $dompdf = new Dompdf($pdfOptions);
	        // Retrieve the HTML generated in our twig file
	        $html = $this->renderView('pdf/factureLocation.html.twig',['location'=>$location]);
	        // Load HTML to Dompdf
	        $dompdf->loadHtml($html);
	        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
	        $dompdf->setPaper('A4', 'portrait');
	        // Render the HTML as PDF
	        $dompdf->render();
	        // Output the generated PDF to Browser (force download)
	        $dompdf->stream("factureFinaleLocation.pdf", [
	            "Attachment" => false
	        ]);
			//return $this->redirectToRoute('recep.location.index');
			//génération de la facture
		}

		/**
		* @Route("/recep/location/imprimer/{id<\d+>}", name="recep.location.imprimer")
		* @return Response
		*/
		public function imprimer(location $location):Response
		{
			$pdfOptions = new Options();
	        $pdfOptions->set('defaultFont', 'Helvetica');
	        $pdfOptions->set('isRemoteEnabled',true);
	        // Instantiate Dompdf with our options
	        $dompdf = new Dompdf($pdfOptions);
	        // Retrieve the HTML generated in our twig file
	        $html = $this->renderView('pdf/factureLocation.html.twig',['location'=>$location]);
	        // Load HTML to Dompdf
	        $dompdf->loadHtml($html);
	        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
	        $dompdf->setPaper('A4', 'portrait');
	        // Render the HTML as PDF
	        $dompdf->render();
	        // Output the generated PDF to Browser (force download)
	        $dompdf->stream("factureLocation.pdf", [
	            "Attachment" => false
	        ]);
			return $this->redirectToRoute('recep.location.consult',['id'=>$location->getId()]);
			//génération de la facture
		}		

		public function checkIfIsBusy($id,\DateTime $debut): ?Location
		{
			$locations= $this->repositoryVar->findBySalle($id);
			foreach ($locations as $location) {
				if($location->getBeginAt() <= $debut && $debut <= $location->getEndAt()){
					return $location;
				}
			}
			return null;
		}

	}

