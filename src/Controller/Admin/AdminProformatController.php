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
	use App\Entity\Proformat;
	use App\Entity\ProformatOffre;
	use App\Entity\ProformatRepas;
	use App\Entity\ProformatSalle;
	use App\Repository\UserRepository;
	use App\Repository\OffreRepository;
	use App\Repository\SalleRepository;
	use App\Repository\RepasRepository;
	use App\Repository\ClientRepository;
	use App\Repository\ProformatRepository;
	use App\Repository\ProformatOffreRepository;
	use App\Repository\ProformatRepasRepository;
	use App\Repository\ProformatSalleRepository;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\JsonResponse;


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
		 * @var ProformatRepository
		 */
		private $repositoryProformat;

		/**
		 * @var ProformatSalleRepository
		 */
		private $repositoryProformatSalle;

		/**
		 * @var ProformatRepasRepository
		 */
		private $repositoryProformatRepas;

		/**
		 * @var ProformatOffreRepository
		 */
		private $repositoryProformatOffre;

		/**
		 * @var ObjectManager
		 */
		private $em;
		
		function __construct(ClientRepository $repository, UserRepository $userRepository ,ObjectManager $em, OffreRepository $oR, SalleRepository $sR, RepasRepository $rR,ProformatRepository $Pr,ProformatSalleRepository $Psr,ProformatRepasRepository $Prr,ProformatOffreRepository $Por)
		{
			$this->repositoryVar = $repository;
			$this->em = $em;
			$this->repositoryUser = $userRepository;
			$this->repositoryOffre = $oR;
			$this->repositorySalle = $sR;
			$this->repositoryRepas = $rR;
			$this->repositoryProformat = $Pr;
			$this->repositoryProformatOffre = $Por;
			$this->repositoryProformatRepas = $Prr;
			$this->repositoryProformatSalle = $Psr;
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
			$motif = $data[0]["motif"];
			$coutProformat = 0;
			$proformat = new Proformat();
			$proformatSalle = new ProformatSalle();
			$proformatOffre = new ProformatOffre();
			$proformatRepas = new ProformatRepas();
			$proformat->setMotif($motif);
			$proformat->setCout($coutProformat);
			$proformat->setMadeAt(new \DateTime());
			$this->em->persist($proformat);
			$this->em->flush();
			if(count($chambres)>0)
			{
				foreach ($chambres as $chambre) {
					$proformatOffre->setOffre($this->repositoryOffre->find($chambre["chambre"]));
					$proformatOffre->setProformat($proformat);
					$proformatOffre->setNuite($chambre["nuite"]);
					$proformatOffre->setCout($this->repositoryOffre->find($chambre["chambre"])->getPrix()*$chambre["nuite"]);
					$coutProformat = $coutProformat+$proformatOffre->getCout();
					$this->em->persist($proformatOffre);
					$this->em->flush();
				}
			}
			if(count($salles)>0)
			{
				foreach ($salles as $salles) {
					$proformatSalle->setSalle($this->repositorySalle->find($salles["salle"]));
					$proformatSalle->setProformat($proformat);
					$proformatSalle->setDay($salles["jours"]);
					$proformatSalle->setCout($this->repositorySalle->find($salles["salle"])->getPrix()*$salles["jours"]);
					$coutProformat = $coutProformat+$proformatSalle->getCout();
					$this->em->persist($proformatSalle);
					$this->em->flush();
				}
			}
			if(count($repas)>0)
			{
				foreach ($repas as $repa) {
					$proformatRepas->setRepas($this->repositoryRepas->find($repa["repas"]));
					$proformatRepas->setProformat($proformat);
					$proformatRepas->setQuantite($repa["quantite"]);
					$proformatRepas->setCout($this->repositoryRepas->find($repa["repas"])->getPrix()*$repa["quantite"]);
					$coutProformat = $coutProformat+($proformatRepas->getCout()*$repa["quantite"]);
					$this->em->persist($proformatRepas);
					$this->em->flush();
				}
			}
			$proformat->setCout($coutProformat);
			$this->em->flush();
			//exit(\Doctrine\Common\Util\Debug::dump($chambres));
			return new JsonResponse(['status' => 'ok',],JsonResponse::HTTP_CREATED);
			
		}

	}