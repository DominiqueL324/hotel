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

	class OperationsIdentification
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

		public function ChangeStateIdentificationAndRoom(Identification $identification):Identification
		{
			$offre = $identification->getOffre()->setDispo(true);
			$identification->setEtat("Terminer");
			$identification->setOffre($offre);
			return $identification;
		}

		
		public function facturationFinale()
		{

		}
	}