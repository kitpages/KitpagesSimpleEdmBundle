<?php
namespace Kitpages\SimpleEdmBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

class Document
{

    private $file;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $isActive;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set reference
     *
     * @param string $reference
     * @return Document
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    
        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Document
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Document
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Document
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Document
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Document
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    public function getFile() {
        return $this->file;
    }

    public function setFile(File $file = null) {
        $this->file = $file;
    }

    public function getFilePath() {
        $idString = (string) $this->getId();
        if (strlen($idString)== 1) {
            $idString = '0'.$idString;
        }
        $dir = substr($idString, 0, 2);
        return $dir.'/'.$this->getId();
    }

    /**
     * @var string
     */
    private $fileOriginalName;


    /**
     * Set fileOriginalName
     *
     * @param string $fileOriginalName
     * @return Document
     */
    public function setFileOriginalName($fileOriginalName)
    {
        $this->fileOriginalName = $fileOriginalName;
    
        return $this;
    }

    /**
     * Get fileOriginalName
     *
     * @return string 
     */
    public function getFileOriginalName()
    {
        return $this->fileOriginalName;
    }

    public function preUpload()
    {
        $file = $this->getFile();
        if ($file != null) {
            $this->setMimeType($file->getMimeType());
            $this->setFileOriginalName($file->getClientOriginalName());
        }
    }

    public function disabled()
    {
        $this->setReference($this->getReference().'-disabled-'.$this->getId());
        $this->setIsActive(false);
    }
    /**
     * @var string
     */
    private $mimeType;


    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return Document
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string 
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}