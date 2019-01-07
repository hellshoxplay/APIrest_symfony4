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
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("client/{id}")
     */
    public function getAction(Request $request)
    {
        $client=$this->getDoctrine ()->getManager ()
            ->getRepository (Client::class)
            ->find ($request->get ('id'));
        return $this->view ($client, Response::HTTP_OK);
    }

    /**
     * @return \FOS\RestBundle\View\View
     * @Rest\View()
     * @Rest\Get("clients")
     */

    public function cgetAction()
    {
        $client=$this->getDoctrine ()->getManager ()
            ->getRepository (Client::class)
            ->findAll ();
        return $this->view ($client, Response::HTTP_OK);
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
     * @return \FOS\RestBundle\View\View
     */
    public function patchAction(Request $request)
    {
        return $this->updateClient ($request,false);
    }

}
