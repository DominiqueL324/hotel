<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccessDeniedHandler extends  AbstractController implements AccessDeniedHandlerInterface 
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $this->addFlash('erreur','Vous navez pas assez de privilèges pour acceder à cette page connecter avec un privilège plus élévé ou revenez en arrière');
        return $this->redirectToRoute('app_login');
    }
}