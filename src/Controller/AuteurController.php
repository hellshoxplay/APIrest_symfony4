<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AuteurController
 * @package App\Controller
 *@Rest\RouteResource(

 *     "Auteur",
 *      pluralize=false
 * )
 *
 */
class AuteurController extends AbstractFOSRestController implements ClassResourceInterface
{
    /**
     * @param Request $request
     * @param $clearmissing
     * @return \FOS\RestBundle\View\View
     */
    private function updateAuteur(Request $request,$clearmissing)
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->find ($request->get ('id'));
        if(empty($auteur)){
            return $this->view (null,Response::HTTP_NOT_FOUND);
        }

        $form=$this->createForm (AuteurType::class, $auteur);
        $form->submit ($request->request->all (),$clearmissing);

        if ($form->isValid ()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->persist ($auteur);
            $em->flush ();
            return $this->view ( $auteur , Response::HTTP_OK);
        }else{
            return $this->view ($form, Response::HTTP_NOT_MODIFIED);
        }
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("/auteur/{id}")
     */
    public function getAction(Request $request)
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->find ($request->get ('id'));

        return $this->view ($auteur, Response::HTTP_OK);
    }

    /**
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("/auteurs")
     *
     */
    public function cgetAction()
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->findAll ();

        return $this->view ($auteur,Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/auteur")
     */
    public function postAction(Request $request)
    {
        $auteur=new Auteur();
        $form=$this->createForm (AuteurType::class, $auteur);
        $form->submit ($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->getRepository ( Auteur::class );
            $em->persist ($auteur);
            $em->flush ();

            return $this->view ( $auteur , Response::HTTP_CREATED );
        }else {
            return $this->view ( $form );
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("auteur/{id}")
     */
    public function deleteAction(Request $request)
    {
        $em=$this->getDoctrine ()->getManager ();
        $auteur=$em->getRepository (Auteur::class)
            ->find ($request->get('id'));

        if(!empty($auteur)) {
            $em->remove ($auteur);
            $em->flush ();
        }
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Put("/auteur/{id}"))
     */
    public function putAction(Request $request)
    {
        return $this->updateAuteur ($request, true);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return \FOS\RestBundle\View\View
     */
    public function patchAction(Request $request, string $id)
    {
        return $this->updateAuteur ($request,false);
    }

}
