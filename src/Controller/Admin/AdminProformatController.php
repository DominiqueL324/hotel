<?php
	namespace App\Controller\Admin;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Entity\User;
	use App\Entity\Client;
	use App\Entity\Offre;
	use App\Entity\Salle;
	use App\Entity\Repas;
	use App\Repository\UserRepository;
	use App\Repository\OffreRepository;
	use App\Repository\SalleRepository;
	use App\Repository\RepasRepository;
	use App\Repository\ClientRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * 
	 */
	class AdminProformatController extends AbstractController
	{
		 /**
		 * @var ClientRepository
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
		 * @var SalleRepository
		 */
		private $repositorySalle;

		/**
		 * @var RepasRepository
		 */
		private $repositoryRepas;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(ClientRepository $repository, UserRepository $userRepository ,ObjectManager $em, OffreRepository $oR, SalleRepository $sR, RepasRepository $rR)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
			$this->repositoryOffre = $oR;
			$this->repositorySalle = $sR;
			$this->repositoryRepas = $rR;
		}

		/**
		* @Route("/recep/proformat", name="recep.proformat.index")
		* @return Response
		*/
		public function index():Response
		{
			$user = $this->getUser();
			$clients = $this->repositoryVar->findAll();
			$offres = $this->repositoryOffre->findAll();
			$salles = $this->repositorySalle->findAll();
			$repas = $this->repositoryRepas->findAll();
			return $this->render('proformat/add.html.twig',['offres'=>$offres,'salles'=>$salles,'repas'=>$repas]); 
		}

		/**
		* @Route("/recep/proformat/json")
		* @return Response
		*/
		public function testApi(Request $request)
		{
			$data = json_decode($request->getContent(),true);
			$chambres = $data[0]["chambres"];
			$salles = $data[0]["salles"];
			$repas = $data[0]["repas"];
			exit(\Doctrine\Common\Util\Debug::dump($chambres));
			return new JsonResponse(['status' => 'ok',],JsonResponse::HTTP_CREATED);
			
		}

	}