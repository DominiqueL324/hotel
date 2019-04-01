<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{

	/**
	* @var UserRepository
	*/
	private $repositoryVar;

	/**
	* @var ObjectManager
	*/
	private $em;

	public function __construct(UserRepository $repository,ObjectManager $manager)
	{
		$this->repositoryVar = $repository;
		$this->em = $manager;
	}

	/**
	* @Route("/Utilisateur", name="user.index")
	* @return Response
	*/
	public function index(): Response
	{
		$users = $this->repositoryVar->findAll();
		return $this->render('users/index.html.twig',['users'=>$users]);
	}

	/**
	* @Route( "/Utilisateur/{slug}-{id<\d+>}", name="user.show", requirements={"slug":"[a-z0-9\-]*"} )
	* @return Response
	*/
	/*public function show($slug, $id): Response
	{
		$property = $this->repositoryVar->find($id);
		if($property->getSlug() !== $slug)
		{
			return $this->redirectToRoute('property.show',[
				'id'=> $property->getId(),
				'slug'=> $property->getSlug()
			],301);
		}
		return $this->render('property/show.html.twig',[
			'property'=>$property,
			'current_menu'=>'properties']);
	}*/

}