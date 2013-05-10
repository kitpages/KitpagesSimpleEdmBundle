<?php
namespace Kitpages\SimpleEdmBundle\EventListener;

use Kitpages\FileSystemBundle\Service\Adapter\AdapterInterface;
use Kitpages\FileSystemBundle\Model\AdapterFile;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\Event;
use Doctrine\Bundle\DoctrineBundle\Registry;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Kitpages\SimpleEdmBundle\Entity\Document;

use Kitpages\SimpleEdmBundle\SimpleEdmException;

class DocumentListener {
    
    ////
    // dependency injection
    ////
    protected $doctrine = null;
    protected $dispatcher = null;
    protected $fileSystem = null;
    protected $tmp_dir = null;

    public function __construct(
        Registry $doctrine,
        EventDispatcher $dispatcher,
        AdapterInterface $fileSystem,
        $tmp_dir
    )
    {
        $this->doctrine = $doctrine;
        $this->dispatcher = $dispatcher;
        $this->fileSystem = $fileSystem;
        $this->tmpDir = $tmp_dir;
    }
    /**
     * @return EventDispatcher $dispatcher
     */
    public function getDispatcher() {
        return $this->dispatcher;
    }  

    /**
     * @return Registry $doctrine
     */
    public function getDoctrine() {
        return $this->doctrine;
    }

    public function getFileSystem() {
        return $this->fileSystem;
    }

    public function upload($entity)
    {
        $file = $entity->getFile();
        if (null === $file) {
            return;
        }
        $this->removeUpload($entity);

        $tempFilePath = tempnam($this->tmpDir, $entity->getId());
        $file->move($this->tmpDir, basename($tempFilePath));

        try {
            $this->fileSystem->copyTempToAdapter(
                $tempFilePath,
                new AdapterFile($entity->getFilePath()),
                $file->getClientMimeType()
            );
        } catch (\Exception $exc) {
            $om = $this->doctrine->getManager();
            $om->remove($entity);
            $om->flush();
            $entity = null;
        }
        unlink($tempFilePath);
        if ($entity == null) {
            throw new SimpleEdmException("fileSystem copy temp to adapter Fail.");
        }

        unset($file);
    }

    public function removeUpload($entity)
    {
        $this->fileSystem->unlink(
            new AdapterFile($entity->getFilePath()),
            true
        );
    }

    ////
    // doctrine events
    ////
    public function postPersist(LifecycleEventArgs $event)
    {    
        $entity = $event->getEntity();
        if ($entity instanceof Document) {
            $this->upload($entity);
        }
    }
    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Document) {
            $this->upload($entity);
        }
    }
    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Document) {
            $this->removeUpload($entity);
        }
    }


}