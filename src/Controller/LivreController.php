<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LivreController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     */
    public function postAction(Request $request)
    {
        $form=$this->createForm (LivreType::class, new Livre());

        $form->submit ($request->request->all());

        if (!$form->isValid()) {
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

    public function getAction($id)
    {
        return $this->view (
            $this->livreRepository->find ($id)
        );
    }
}

