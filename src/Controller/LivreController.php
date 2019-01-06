<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
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
     */
    public function postAction(Request $request)
    {
        $form=$this->createForm (LivreType::class, new Livre());

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
    public function getAction(string $id)
    {
        return $this->view (
            $this->findLivreById ($id)
        );
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function cgetAction()
    {
        return $this->view (
            $this->livreRepository->findAll ()
    );
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
        $existingLivre=$this->findLivreById ($id);

        $form=$this->createForm (LivreType::class, $existingLivre);
        $form->submit ($request->request->all(), false);

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
        $livre=$this->findLivreById ($id);

        $this->entityManager->remove ($livre);
        $this->entityManager->flush ();

        return $this->view (null,Response::HTTP_NO_CONTENT);
    }

}

