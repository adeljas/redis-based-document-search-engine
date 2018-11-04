<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation\Type;

class Document {

    /**
     * @var $identifier string
     * @Type("string")
     */
    private $identifier;

    /**
     * @var $value string
     * @Type("string")
     */
    private $contents;

    function __construct(string $identifier, string $contents)
    {
        $this->identifier = $identifier;
        $this->contents = $contents;
    }

    function getIdentifier()
    {
        return $this->identifier;
    }

    function getContents()
    {
        return $this->contents;
    }



}