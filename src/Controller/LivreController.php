<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Livre;
use App\Form\AuteurType;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LivreRepository
     */
    private $livreRepository;

    /**
     * LivreController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (EntityManagerInterface $entityManager, LivreRepository $livreRepository)
    {
        $this->entityManager = $entityManager;
        $this->livreRepository=$livreRepository;
    }

    /**
     * @param $id
     * @return Livre|null
     */
    private function findLivreById(string $id)
    {
         $livre = $this->livreRepository->find ($id);

         if(null===$livre){
             throw new NotFoundHttpException();
         }

         return $livre;
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
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
      * @Rest\Get("/livre/{id}")
     */
    public function getAction(string $id)
    {
        return $this->view (
            $this->findLivreById ($id)
        );
    }

    /**
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("/livres")
     */
    public function cgetAction()
    {
        return $this->view (
            $this->livreRepository->findAll ()
    );
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
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function putAction(Request $request, string $id)
    {
        $existingLivre=$this->findLivreById ($id);

        $form=$this->createForm (LivreType::class, $existingLivre);
        $form->submit ($request->request->all ());

        if ($form->isValid ()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->merge ( $existingLivre );
            $em->flush ();
            return $this->view ( null , Response::HTTP_NO_CONTENT );
        }else{
            return $this->view ($form);
        }
    }

    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View|null|object|\Symfony\Component\Form\FormInterface
     * @Rest\View()
     * @Rest\Patch()
     */
    public function patchAction(Request $request )
    {
        $livre= $this->getDoctrine ()->getManager ()
            ->getRepository (Livre::class)
            ->find ($request->get ('id'));

        if(empty($livre)){

            return $this->view (null, Response::HTTP_NOT_FOUND);
        }

        $form=$this->createForm (LivreType::class, $livre);
        $form->submit ($request->request->all(), false);

        if($form->isValid ()){
            $em=$this->getDoctrine ()->getManager ();
            $em->merge ($livre);
            $em->flush();

            return $livre;
        }else{
            return $form;
        }

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
  
   /**
     * @param string $id
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(string $id)
    {
        $livre=$this->findLivreById ($id);

        $this->entityManager->remove ($livre);
        $this->entityManager->flush ();

        return $this->view (null,Response::HTTP_NO_CONTENT);
    }

}

