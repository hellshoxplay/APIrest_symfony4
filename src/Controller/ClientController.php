<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ClientController
 * @package App\Controller
 * @Rest\RouteResource(
 *     "Client",
 *     pluralize=false
 * )
 */
class ClientController extends AbstractFOSRestController implements ClassResourceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * ClientController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (EntityManagerInterface $entityManager, ClientRepository $clientRepository)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository=$clientRepository;
    }
    /**
     * @param $id
     * @return Client|null
     */
    private function findClientById($id)
    {
        $client = $this->clientRepository->find($id);

        if(null===$client){
            throw new NotFoundHttpException();
        }

        return $client;
    }
    /**
     * @param $id
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("client/{id}")
     */
    public function getAction($id)
    {
        return $this->view (
            $this->clientRepository->find ($id)
        );
    }

    /**
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("clients")
     */

    public function cgetAction()
    {
        return $this->view (
            $this->clientRepository->findAll ()
        );
    }
    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View|Response
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/client")
     */
    public function postAction(Request $request)
    {
        $client=new Client();
        $form=$this->createForm (ClientType::class, $client);
        $form->submit ($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->getRepository ( Client::class );
            $em->persist ($client);
            $em->flush ();

            return $this->view ( $client , Response::HTTP_CREATED );
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
        $existingclient=$this->clientRepository->find($id);

        $form=$this->createForm (ClientType::class, $existingclient);
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
        $existingclient=$this->clientRepository->find($id);

        $form=$this->createForm (ClientType::class, $existingclient);
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
        $client=$this->findClientById($id);

        $this->entityManager->remove ($client);
        $this->entityManager->flush ();

        return $this->view (null,Response::HTTP_NO_CONTENT);
    }

}
