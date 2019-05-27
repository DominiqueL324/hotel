<?php
	namespace App\Controller\Charts;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Doctrine\Common\Persistence\ObjectManager;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use App\Repository\OffreRepository;
	use App\Repository\IdentificationRepository;
	/*use App\Entity\User;
	use App\Entity\Client;
	use App\Entity\Repas;
	use App\Entity\Consomation;
	use App\Repository\UserRepository;
	use App\Repository\ClientRepository;
	use App\Repository\RepasRepository;
	use App\Repository\ConsomationRepository;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Twig\Environment;
	use Dompdf\Dompdf;
	use Dompdf\Options;
	*/

	/**
	 * 
	 */
	class testChartsController extends AbstractController
	{

		/**
		 * @var ObjectManager
		 */
		private $em;

		/**
		 * @var OffreRepository
		 */
		private $repositoryOffre;

		/**
		 * @var IdentificationRepository
		 */
		private $repositoryIdentification;

		
		function __construct(ObjectManager $em, OffreRepository $Or,IdentificationRepository $Ir)
		{
			$this->em = $em;
			$this->repositoryOffre = $Or;
			$this->repositoryIdentification = $Ir;
		}

		/**
		* @Route("recep/charts/test", name="charts.test")
		* @return Response
		*/
		public function index():Response
		{
			return $this->render('charts/test.html.twig'); 
		}

		/**
		* @Route("recep/charts/test/check", name="charts.test.check")
		* @return Response
		*/
		public function check(Request $request):Response
		{
			$listeNombre;
			$listeId = $this->repositoryOffre->findOffreId();
			foreach ($listeId as $key => $value) {

				foreach ($value as $key1 => $value1) {
					if ($key1 == "id") {
						$result = $this->repositoryIdentification->getTotal($value1);
						$listeNombre["nombre"][]= (int)$result;
					}else{
						$listeNombre["libelle"][]= $value1;
					}
				}
			}
			//exit(\Doctrine\Common\Util\Debug::dump($listeId));
			return new JsonResponse(['status' => $listeNombre,],JsonResponse::HTTP_CREATED);
		}
	}

