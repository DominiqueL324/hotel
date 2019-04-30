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
		* @Route("/recep/consomation/create", name="recep.consomation.new")
		* @return Response
		*/
		public function new(Request $request):Response
		{
			if($this->isCsrfTokenValid('add',$request->get('_token')))
			{
				if(null!==$request->get('client') && null!==$request->get('repas'))
					{
						$client = new Client();
						$client = $this->repositoryClient->find($request->get('client'));
						$repas = new Repas();
						$repas = $this->repositoryRepas->find($request->get('repas'));
						$consomation = new consomation();

						$consomation->setRepas($repas);
						$consomation->setCout($request->get('cout'));
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
	}

