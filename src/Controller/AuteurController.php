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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AuteurRepository
     */
    private $auteurRepository;

    /**
     * AuteurController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (EntityManagerInterface $entityManager, AuteurRepository $auteurRepository)
    {
        $this->entityManager = $entityManager;
        $this->auteurRepository=$auteurRepository;
    }

    /**
     * @param $id
     * @return Auteur|null
     */
    private function findAuteurById($id)
    {
        $auteur = $this->auteurRepository->find ($id);

        if(null===$auteur){
            throw new NotFoundHttpException();
        }

        return $auteur;
    }

    /**
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("/auteur/{id}")
     */
    public function getAction($id)
    {
        return $this->view (
            $this->findAuteurById ($id)
        );
    }

    /**
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("/auteurs")
     *
     */
    public function cgetAction()
    {
        return $this->view (
            $this->auteurRepository->findAll ()
        );
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
     * @param Request $request
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function putAction(Request $request, $id)
    {
        $existingAuteur=$this->findAuteurById ($id);

        $form=$this->createForm (AuteurType::class, $existingAuteur);
        $form->submit ($request->request->all ());

        if(false===$form->isValid ()){
            return $this->view ($form);
        }

        $this->entityManager->flush ();

        return $this->view (null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return \FOS\RestBundle\View\View
     */
    public function patchAction(Request $request, string $id)
    {
        $existingAuteur=$this->findAuteurById ($id);

        $form=$this->createForm (AuteurType::class, $existingAuteur);
        $form->submit ($request->request->all ());

        if(false===$form->isValid ()){
            return $this->view ($form);
        }

        $this->entityManager->flush ();

        return $this->view (null, Response::HTTP_NO_CONTENT);
    }
  
   /**
     * @param string $id
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction(string $id)
    {
        $auteur=$this->findAuteurById ($id);

        $this->entityManager->remove ($auteur);
        $this->entityManager->flush ();

        return $this->view (null,Response::HTTP_NO_CONTENT);

    }

}
