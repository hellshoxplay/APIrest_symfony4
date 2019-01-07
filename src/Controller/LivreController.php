<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Livre;
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
     * @Rest\View(serializerGroups={"livre"})
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
     * @Rest\View(serializerGroups={"livre"})
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
     * @Rest\View(statusCode=Response::HTTP_CREATED,serializerGroups={"livre"})
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
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"livre"})
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

// on passe aux routes de type "sub ressources" de la ManyToOne Livre <--> Auteur

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View(serializerGroups={"livre","auteur"})
     * @Rest\get("auteur/{id}/livres")
     */
    public function getLivresAuteurAction(Request $request)
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->find ($request->get ('id'));

        if(empty($auteur)){
            return $this->view (null, Response::HTTP_NOT_FOUND);
        }

        return $this->view ($auteur,Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View(statusCode=Response::HTTP_CREATED,serializerGroups={"livre", "auteur"})
     * @Rest\Post("/auteur/{id}/livres")
     */
    public function postLivresAuteurAction(Request $request)
    {
        $auteur=$this->getDoctrine ()->getManager ()
            ->getRepository (Auteur::class)
            ->find ($request->get ('id'));

        if(empty($auteur)){
            return $this->view (null, Response::HTTP_BAD_REQUEST);
        }

        $livres=new Livre();
        $livres->setAuteur ($auteur);
        $form=$this->createForm (LivreType::class, $livres);
        $form->submit ($request->request->all ());

        if($form->isValid ()){

            $em=$this->getDoctrine ()->getManager ();
            $em->persist ($livres);
            $em->flush ();

            return $this->view ($auteur,Response::HTTP_CREATED);

        }else {

            return $this->view ( $form , Response::HTTP_NOT_FOUND );
        }
    }
//
////ici on passe aux routes de type "Subressources" de la ManyToMany Livre <--> Client
//    /**
//     * @param Request $request
//     * @return \FOS\RestBundle\View\View
//     * @Rest\View(serializerGroups={"livre","client"})
//     * @Rest\get("livre/{id}/clients")
//     */
//    public function getLivresClientsAction(Request $request)
//    {
//        return $this->view ($clients,Response::HTTP_OK);
//    }
//
//    /**
//     * @param Request $request
//     * @return \FOS\RestBundle\View\View
//     * @Rest\View(statusCode=Response::HTTP_CREATED,serializerGroups={"livre", "client"})
//     * @Rest\Post("/livre/{id}/clients")
//     */
//    public function postLivresClientsAction(Request $request)
//    {
//       return $this->view ( $clients , Response::HTTP_CREATED );
//
//    }

}

