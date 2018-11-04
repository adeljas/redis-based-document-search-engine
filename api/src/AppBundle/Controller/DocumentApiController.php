<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use http\Env\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Post;
use Doctrine\DBAL\Exception as DBALExceptions;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use AppBundle\Util\RedisDocumentHelper;


class DocumentApiController extends FOSRestController
{

    private $redisDocumentHelper;

    public function __construct(RedisDocumentHelper $redisDocumentHelper)
    {
        $this->redisDocumentHelper = $redisDocumentHelper;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="fetches one document from redis by its key",
     *  section="Documents"
     * )
     * @param $identifier string
     * @Route("/api/document/{identifier}", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDocument($identifier)
    {
        $document = $this->redisDocumentHelper->getDocument($identifier);
        $view = $this->view($document);
        return $this->handleView($view);
    }


    /**
     * adds a document to the index
     * @var $document Document
     * @ApiDoc(
     *  resource=true,
     *  description="adds a document",
     *  section="Documents"
     * )
     * @ParamConverter("document", converter="fos_rest.request_body")
     * @Post("/api/document")
     * @RequestParam(name="identifier", requirements={"rule" = ".*", "error_message" = "invalid document id"}, strict=true, nullable=false, description="Custom Document Identifier")
     * @RequestParam(name="contents", requirements={"rule" = ".*", "error_message" = "invalid document contents"}, strict=true, nullable=false, description="Document Contents")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Ehann\RediSearch\Exceptions\FieldNotInSchemaException
     */
    public function addDocument(Document $document)
    {
        $document = $this->redisDocumentHelper->addDocument(
            $document->getIdentifier(),
            $document->getContents()
        );

        $view = $this->view($document);
        return $this->handleView($view);
    }


    /**
     * @ApiDoc(
     *  resource=true,
     *  description="searches for content within the documents",
     *  section="Search"
     * )
     * @param $query string
     * @Route("/api/document/search/{query}", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function search($query)
    {
        $document = $this->redisDocumentHelper->search($query);

        $view = $this->view($document);
        return $this->handleView($view);
    }


    /**
     * deletes a document by its identiifier
     * @ApiDoc(
     *  resource=true,
     *  description="deletes a document by its identifier",
     *  section="Documents"
     * )
     * @param $identifier string
     * @Route("/api/document/{identifier}", methods={"DELETE"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteDocument($identifier)
    {
        $document = $this->redisDocumentHelper->deleteDocument($identifier);

        if ($document->getCount() > 0) {
            $view = $this->view($document);
        } else {
            $view = $this->view($document, HttpResponse::HTTP_NOT_FOUND);
        }

        return $this->handleView($view);
    }

}

