<?php
	namespace App\Controller\Inventaires;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Client;
	use App\Entity\Offre;
	use App\Entity\Identification;
	use App\Repository\UserRepository;
	use App\Repository\ClientRepository;
	use App\Repository\OffreRepository;
	use App\Repository\IdentificationRepository;
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
	class InventaireIdentificationController extends AbstractController
	{

		/**
		 * @var ObjectManager
		 */
		private $em;

		 /**
		 * @var IdentificationRepository
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
		 * @var OffreRepository
		 */
		private $repositoryOffre;
		
		function __construct(ClientRepository $c,UserRepository $u,OffreRepository$r,IdentificationRepository $repository,ObjectManager $em)
		{
			$this->repositoryVar = $repository;
			$this->repositoryClient = $c;
			$this->repositoryUser = $u;
			$this->repositoryOffre = $r;
			$this->em = $em;
		}

		/**
		* @Route("/admin/identification/inventaire", name="admin.identification.inventaire")
		* @return Response
		*/
		public function index():Response
		{
			$Identifications = $this->repositoryVar->findAll();
			$clients = $this->repositoryClient->findAll();
			$Offres = $this->repositoryOffre->findAll();
			$users = $this->repositoryUser->findAll();
			$cout = $this->repositoryVar->getPrixTotal();
			return $this->render('Identification/inventaire.html.twig',[
				'identifications'=>$Identifications,
				'users'=>$users,
				'offres'=>$Offres,
				'clients'=>$clients,
				'cout'=>$cout,
			]); 
		}

		/**
		* @Route("/admin/identification/inventaire/recherche", name="admin.identification.inventaire.find")
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
				'offre'=>$request->get('offres'),
				'date1'=>$date1,
				'date2'=>$date2,
			];
			$Identifications = $this->repositoryVar->finder($result);
			$clients = $this->repositoryClient->findAll();
			$Offres = $this->repositoryOffre->findAll();
			$users = $this->repositoryUser->findAll();
			$cout = $this->repositoryVar->getPrixTotal();
			return $this->render('Identification/inventaire.html.twig',[
				'identifications'=>$Identifications,
				'users'=>$users,
				'offres'=>$Offres,
				'clients'=>$clients,
				'cout'=>$cout,
			]);
	
		}
	}

