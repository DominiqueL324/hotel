<?php
	namespace App\Controller\Inventaires;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Client;
	use App\Entity\Repas;
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
	class InventaireConsomationController extends AbstractController
	{

		/**
		 * @var ObjectManager
		 */
		private $em;

		 /**
		 * @var ConsomationRepository
		 */
		private $repositoryVar;

		/**
		 * @var ClientRepository
		 */
		private $repositoryClient;

		/**
		 * @var UserRepository
		 */
		private $repositoryUser;

		/**
		 * @var RepasRepository
		 */
		private $repositoryRepas;
		
		function __construct(ClientRepository $c,UserRepository $u,RepasRepository$r,ConsomationRepository $repository,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositoryClient = $c;
			$this->repositoryUser = $u;
			$this->repositoryRepas = $r;
			$this->em = $em;
		}

		/**
		* @Route("/admin/consomation/inventaire", name="admin.consomation.inventaire")
		* @return Response
		*/
		public function index():Response
		{
			$consomations = $this->repositoryVar->findAll();
			$clients = $this->repositoryClient->findAll();
			$repas = $this->repositoryRepas->findAll();
			$users = $this->repositoryUser->findAll();
			return $this->render('consomation/inventaire.html.twig',[
				'consomations'=>$consomations,
				'users'=>$users,
				'repas'=>$repas,
				'clients'=>$clients,
			]); 
		}

		/**
		* @Route("/admin/consomation/inventaire/recherche", name="admin.consomation.inventaire.find")
		* @return Response
		*/
		public function find(Request $request):Response
		{
			$result=[
				'client'=>$request->get('client'),
				'user'=>$request->get('users'),
				'repas'=>$request->get('repas'),
			];
			$consomations = $this->repositoryVar->finder($result);
			$clients = $this->repositoryClient->findAll();
			$repas = $this->repositoryRepas->findAll();
			$users = $this->repositoryUser->findAll();
			return $this->render('consomation/inventaire.html.twig',[
				'consomations'=>$consomations,
				'users'=>$users,
				'repas'=>$repas,
				'clients'=>$clients,
			]);
	
		}
	}

