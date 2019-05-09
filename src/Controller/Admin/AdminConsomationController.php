<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Repas;
	use App\Entity\Client;
	use App\Entity\Consomation;
	use App\Repository\UserRepository;
	use App\Repository\ClientRepository;
	use App\Repository\RepasRepository;
	use App\Repository\ConsomationRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\HttpFoundation\Request;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Twig\Environment;
	use Dompdf\Dompdf;
	use Dompdf\Options;
	use Symfony\Component\HttpFoundation\JsonResponse;

	/**
	 * 
	 */
	class AdminConsomationController extends AbstractController
	{
		/**
		 * @var ConsomationRepository
		 */
		private $repositoryVar;

		/**
		 * @var RepasRepository
		 */
		private $repositoryRepas;

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
		
		function __construct(ClientRepository $repoClient, ConsomationRepository $repository,RepasRepository $repositoryrepas, UserRepository $userRepository ,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositoryRepas = $repositoryrepas;
			$this->repositoryClient = $repoClient;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
		}

		/**
		* @Route("/recep/consomation", name="recep.consomation.index")
		* @return Response
		*/
		public function index():Response
		{
			$consomations = $this->repositoryVar->findAll();
			return $this->render('consomation/index.html.twig',compact('consomations')); 
		}


		/**
		* @Route("/recep/consomation/etape1", name="recep.consomation.etape1")
		* @return Response
		*/
		public function etape1():Response
		{
			$clients = $this->repositoryClient->findAll();
			return $this->render('consomation/add1.html.twig',compact('clients')); 
		}

		/**
		* @Route("/recep/consomation/etape2/{id<\d+>}", name="recep.consomation.etape2")
		* @return Response
		*/
		public function etape2(Client $client):Response
		{
			$repas = $this->repositoryRepas->findAll();
			return $this->render('consomation/add2.html.twig',['repas'=>$repas,'client'=>$client]); 
		}


		/**
		* @Route("/recep/consomation/editLauncher/{id<\d+>}", name="recep.consomation.editLauncher")
		* @return Response
		*/
		public function editLauncher(Consomation $consomation):Response
		{
			$repas = $this->repositoryRepas->findAll();
			return $this->render('consomation/edit.html.twig',['repas'=>$repas,'consomation'=>$consomation]); 
		}

		/**
		* @Route("/recep/consomation/consult/{id<\d+>}", name="recep.consomation.consult")
		* @return Response
		*/
		public function consult(Consomation $consomation):Response
		{
			return $this->render('consomation/consult.html.twig',['consomation'=>$consomation]); 
		}	

		/**
		* @Route("/recep/consomation/create", name="recep.consomation.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if($request->get('quantite')<=0){
					$this->addFlash('erreur','quantite non valide');
					return $this->redirectToRoute('recep.consomation.etape2',['id'=>$request->get('client')]);
				}
				if(null!==$request->get('client') && null!==$request->get('repas'))
					{
						$client = new Client();
						$client = $this->repositoryClient->find($request->get('client'));
						$repas = new Repas();
						$repas = $this->repositoryRepas->find($request->get('repas'));
						$consomation = new consomation();
						$consomation->setRepas($repas);
						$consomation->setQuantite($request->get('quantite'));
						$consomation->setCout($repas->getPrix()*$consomation->getQuantite());
						$consomation->setCLient($client);
						$consomation->setMadeAt(new \DateTime());
						$consomation->setUser($this->getUser());
						$this->em->persist($consomation);
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.consomation.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.consomation.etape2',['id'=>$request->get('client')]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.consomation.etape2',['id'=>$request->get('client')]);
			}
		}

		/**
		* @Route("/recep/consomation/edit/{id}", name="recep.consomation.edit")
		* @return Response
		*/
		public function edit(Consomation $consomation,Request $request):Response
		{
			if($this->isCsrfTokenValid('edit',$request->get('_token')))
			{
				if($request->get('quantite')<=0){
					$this->addFlash('erreur','quantite non valide');
					return $this->redirectToRoute('recep.consomation.etape2',['id'=>$request->get('client')]);
				}
				if(null!==$request->get('client') && null!==$request->get('repas'))
					{
						$client = new Client();
						$client = $this->repositoryClient->find($request->get('client'));
						$repas = new Repas();
						$repas = $this->repositoryRepas->find($request->get('repas'));
						$consomation->setRepas($repas);
						$consomation->setQuantite($request->get('quantite'));
						$consomation->setCout($repas->getPrix()*$consomation->getQuantite());
						$consomation->setCLient($client);
						$consomation->setMadeAt(new \DateTime());
						$consomation->setUser($this->getUser());
						$this->em->flush();
						$this->addFlash('success',"Opération efféctué avec success");
						return $this->redirectToRoute('recep.consomation.index');
					}else
					{
						$this->addFlash('erreur','Toutes les données sont obliagtoires et doivent être valides');
						return $this->redirectToRoute('recep.consomation.editLauncher',['id'=>$consomation->getId()]);
					}

			}else{
					$this->addFlash('erreur','Formulaire non reconnus');
					return $this->redirectToRoute('recep.consomation.editLauncher',['id'=>$consomation->getId()]);
			}
		}

		/**
		* @Route("/recep/consomation/delete/{id}", name="recep.consomation.delete")
		* @return Response
		*/
		public function delete(Consomation $consomation,Request $request):Response
		{
			
		}

		/**
		* @Route("/recep/consomation/print/{id<\d+>}", name="recep.consomation.print")
		* @return Response
		*/
		public function facturer(Consomation $consomation):Response
		{
			
			$this->em->flush();
			$pdfOptions = new Options();
	        $pdfOptions->set('defaultFont', 'Helvetica');
	        $pdfOptions->set('isRemoteEnabled',true);
	        // Instantiate Dompdf with our options
	        $dompdf = new Dompdf($pdfOptions);
	        // Retrieve the HTML generated in our twig file
	        $html = $this->renderView('pdf/factureRepas.html.twig',['consomation'=>$consomation]);
	        // Load HTML to Dompdf
	        $dompdf->loadHtml($html);
	        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
	        $dompdf->setPaper('A4', 'portrait');
	        // Render the HTML as PDF
	        $dompdf->render();
	        // Output the generated PDF to Browser (force download)
	        $dompdf->stream("factureRepas.pdf", [
	            "Attachment" => true
	        ]);
			//return $this->redirectToRoute('recep.identification.index');
			//génération de la facture
		}

		/**
		* @Route("/recep/consomation/json",methods={"POST"})
		* @return Response
		*/
		public function testApi(Request $request)
		{
			$data = json_decode($request->getContent(),true);
			//exit(\Doctrine\Common\Util\Debug::dump($data));
			return new JsonResponse(['status' => 'ok',],JsonResponse::HTTP_CREATED);
			$this->addFlash('success',"Opération efféctué avec success");
			//return $this->redirectToRoute('recep.consomation.index');*/
		}

	}

