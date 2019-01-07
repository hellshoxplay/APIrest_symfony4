<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Livre;
use App\Form\AuteurType;
use App\Form\LivreType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class LivreController
 * @package App\Controller
 * @Rest\RouteResource(
 *     "Livre",
 *     pluralize=false
 * )
 */
class LivreController extends AbstractFOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param $clearmissing
     * @return \FOS\RestBundle\View\View
     */
    private function updateLivre(Request $request,$clearmissing)
    {
        $livre=$this->getDoctrine ()->getManager ()
            ->getRepository (Livre::class)
            ->find ($request->get ('id'));
        if(empty($livre)){
            return $this->view (null,Response::HTTP_NOT_FOUND);
        }

        $form=$this->createForm (LivreType::class, $livre);
        $form->submit ($request->request->all (),$clearmissing);

        if ($form->isValid ()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->persist($livre);
            $em->flush ();

            return $this->view ( $livre , Response::HTTP_OK);
        }else{
            return $this->view ($form, Response::HTTP_NOT_MODIFIED);
        }
    }

     /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
      * @Rest\Get("/livre/{id}")
     */
    public function getAction(Request $request)
    {
        $livre=$this->getDoctrine ()->getManager ()
            ->getRepository (Livre::class)
            ->find ($request->get ('id'));

        return $this->view ($livre,Response::HTTP_OK);
    }

    /**
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("/livres")
     */
    public function cgetAction()
    {
        $livre=$this->getDoctrine ()->getManager ()
            ->getRepository (Livre::class )
            ->findAll ();
        return $this->view ($livre, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/livre")
     */
    public function postAction(Request $request)
    {
        $livre=new Livre();
        $form=$this->createForm (LivreType::class, $livre);
        $form->submit ($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->getRepository ( Livre::class );
            $em->persist ($livre);
            $em->flush ();

            return $this->view ( $livre , Response::HTTP_CREATED );
        }else {
            return $this->view ( $form );
        }
    }

    /**
     * @param Request $request
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("livre/{id}")
     */
    public function deleteAction(Request $request)
    {
        $em=$this->getDoctrine ()->getManager ();
        $livre=$em->getRepository (Livre::class)
                ->find ($request->get('id'));

        if(!empty($livre)) {
            $em->remove ($livre);
            $em->flush ();
        }
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\Put("/livre/{id}")
     */
    public function putAction(Request $request)
    {
        return $this->updateLivre ($request,true);
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\Patch("/livre/{id}")
     */
    public function patchAction(Request $request )
    {
        return $this->updateLivre ($request,false);

    }

    /**
     * @param Request $request
     * @return int|null|object
     * @Rest\View()
     * @Rest\Get("/livre/{id}/auteur")
     */
    public function getAuteursAction(Request $request)
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->find ($request->get ('id'));

        if(empty($auteur)){
            return Response::HTTP_NOT_FOUND;
        }
        return $auteur;
    }

    /**
     * @param Request $request
     * @return int|null|object
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Patch("/livre/{id}/auteur")
     */
    public function patchAuteursAction(Request $request)
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->find ($request->get ('id'));

        if (empty($auteur)){
            return Response::HTTP_NOT_FOUND;
        }

        $auteur=new Auteur();
        $auteur->setNom ($auteur['nom']);
        $auteur->setPrenom ($auteur['prenom']);
        $form=$this->createForm (AuteurType::class);

        $form->submit($request->request->all ());

        if($form->isValid()){

            $em=$this->getDoctrine ()->getManager ();
            $em->persist ($auteur);
            $em->flush ();

            return $auteur;
        }else {
            return $form;
        }
    }
  


}

