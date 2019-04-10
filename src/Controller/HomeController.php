<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use  Twig\Environment;
use Symfony\Component\Routing\Annotation\Route; 
use Dompdf\Dompdf;
use Dompdf\Options; 
use App\Repository\ReservationRepository;
use App\Entity\Reservation;
use App\Entity\User;

/**
 * 
 */
class HomeController extends AbstractController
{
	/**
	* @Route("/home", name="home")
	* @param PropertyRepository $repository
	* @return Response
	*/
	public function index(): Response{
        $user = $this->getUser();
       return $this->render('pages/home.html.twig',compact('user'));
	}

	/**
	* @Route("/recep/print/{id}", name="print")
	* @return Response
	*/
	public function generatePdf(ReservationRepository $repoReservation,$id){
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Helvetica');
        $pdfOptions->set('isRemoteEnabled',true);
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $reservation = new Reservation();
        $reservation = $repoReservation->find($id);
        $html = $this->renderView('pdf/default.html.twig', [
            'reservation' => $reservation
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf1.pdf", [
            "Attachment" => false
        ]);
        //$reservation = $repoReservation->find($id);
        //return $this->render('pdf/default.html.twig',compact('reservation'));
	}
	
}