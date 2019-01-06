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
     * @param Request $request
     * @param $clearmissing
     * @return \FOS\RestBundle\View\View
     */
    public function updateClient(Request $request,$clearmissing)
    {
        $client=$this->getDoctrine ()->getManager ()
            ->getRepository (Client::class)
            ->find ($request->get ('id'));
        if(empty($client)){
            return $this->view (null,Response::HTTP_NOT_FOUND);
        }

        $form=$this->createForm (ClientType::class, $client);
        $form->submit ($request->request->all (),$clearmissing);

        if ($form->isValid ()) {
            $em = $this->getDoctrine ()->getManager ();
            $em->merge ($client);
            $em->flush ();
            return $this->view ( $client , Response::HTTP_OK);
        }else{
            return $this->view ($form, Response::HTTP_NOT_MODIFIED);
        }
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
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("client/{id}")
     */
    public function deleteAction(Request $request)
    {
        $em=$this->getDoctrine ()->getManager ();
        $client=$em->getRepository (Client::class)
            ->find ($request->get('id'));

        if(!empty($client)) {
            $em->remove ($client);
            $em->flush ();
        }
    }
    /**
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Put("/client/{id}"))
     */
    public function putAction(Request $request)
    {
     return $this->updateClient ($request,true);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return \FOS\RestBundle\View\View
     */
    public function patchAction(Request $request)
    {
        return $this->updateClient ($request,false);
    }

}
