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
            $this->auteurRepository->find ($id)
        );
    }

    public function cgetAction()
    {
        return $this->view (
            $this->auteurRepository->findAll ()
        );
    }


}
