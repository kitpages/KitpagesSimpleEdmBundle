<?php
namespace Kitpages\SimpleEdmBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Kitpages\SimpleEdmBundle\Entity\Document;

class DocumentEvent extends AbstractEvent
{
    public function __construct()
    {
    }

    /**
     * @Param Document $document
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }
    /**
     * return Document
     */
    public function getDocument()
    {
        return $this->document;
    }
}
