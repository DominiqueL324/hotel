<?php
	namespace App\Controller\Inventaires;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Client;
	use App\Entity\Salle;
	use App\Entity\Location;
	use App\Repository\UserRepository;
	use App\Repository\ClientRepository;
	use App\Repository\SalleRepository;
	use App\Repository\LocationRepository;
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
	class InventaireLocationController extends AbstractController
	{

		/**
		 * @var ObjectManager
		 */
		private $em;

		 /**
		 * @var LocationRepository
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
		 * @var SalleRepository
		 */
		private $repositorySalle;
		
		function __construct(ClientRepository $c,UserRepository $u,SalleRepository$r,LocationRepository $repository,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositoryClient = $c;
			$this->repositoryUser = $u;
			$this->repositorySalle = $r;
			$this->em = $em;
		}

		/**
		* @Route("/admin/location/inventaire", name="admin.location.inventaire")
		* @return Response
		*/
		public function index():Response
		{
			$locations = $this->repositoryVar->findAll();
			$clients = $this->repositoryClient->findAll();
			$salles = $this->repositorySalle->findAll();
			$users = $this->repositoryUser->findAll();
			$cout = $this->repositoryVar->getPrixTotal();
			return $this->render('location/inventaire.html.twig',[
				'locations'=>$locations,
				'users'=>$users,
				'salles'=>$salles,
				'clients'=>$clients,
				'cout'=>$cout,
			]); 
		}

		/**
		* @Route("/admin/location/inventaire/recherche", name="admin.location.inventaire.find")
		* @return Response
		*/
		public function find(Request $request):Response
		{
			if($request->get('date1')!=null and $request->get('date2')!=null){
				$date1 = new \DateTime($request->get('date1'));
				$date2 = new \DateTime($request->get('date2'));
			}else
			{
				$date1="vide";
				$date2="vide";
			}

			$result=[
				'client'=>$request->get('client'),
				'user'=>$request->get('users'),
				'salle'=>$request->get('salles'),
				'date1'=>$date1,
				'date2'=>$date2,			];
			$locations = $this->repositoryVar->finder($result);
			$clients = $this->repositoryClient->findAll();
			$salles = $this->repositorySalle->findAll();
			$users = $this->repositoryUser->findAll();
			$cout = $this->repositoryVar->getPrixTotal();
			return $this->render('location/inventaire.html.twig',[
				'locations'=>$locations,
				'users'=>$users,
				'salles'=>$salles,
				'clients'=>$clients,
				'cout'=>$cout,
			]);
	
		}
	}

