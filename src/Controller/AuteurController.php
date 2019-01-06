<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
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
class AuteurController extends FOSRestController implements ClassResourceInterface
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
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     */
    public function postAction(Request $request)
    {
        $form=$this->createForm (AuteurType::class, new Auteur());

        $form->submit ($request->request->all());

        if (false===$form->isValid()) {
            return $this->handleView (
                $this->view($form)
            );
        }

        $this->entityManager->persist ($form->getData ());
        $this->entityManager->flush ();

        return $this->view (
            [
                'status' =>'ok',
            ]
        );
    }

    /**
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function getAction($id)
    {
        return $this->view (
            $this->findAuteurById ($id)
        );
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function cgetAction()
    {
        return $this->view (
            $this->auteurRepository->findAll ()
        );
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
    public function Delete(string $id)
    {
        $auteur=$this->findAuteurById ($id);

        $this->entityManager->remove ($auteur);
        $this->entityManager->flush ();

        return $this->view (null,Response::HTTP_NO_CONTENT);

    }

}
