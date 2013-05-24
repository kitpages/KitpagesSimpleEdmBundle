<?php

namespace Kitpages\SimpleEdmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kitpages\SimpleEdmBundle\Form\DocumentFormType;
use Kitpages\SimpleEdmBundle\Entity\Document;
use Kitpages\DataGridBundle\Model\GridConfig;
use Kitpages\DataGridBundle\Model\Field;
use Kitpages\FileSystemBundle\Model\AdapterFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Kitpages\SimpleEdmBundle\Event\DocumentEvent;
use Kitpages\SimpleEdmBundle\KitpagesSimpleEdmEvents;

class DocumentController extends Controller
{

    public function documentAddAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SIMPLEEDM_WRITER')) {
            throw new AccessDeniedException();
        }
        $entity = new Document();

        $documentForm = new DocumentFormType();
        // build basic form
        $form   = $this->createForm($documentForm, $entity);
        $formHandler = $this->container->get('kitpages_simpleedm.document.form.handler');
        $process = $formHandler->process($form, $entity);

        if ($process) {
            return $this->redirect($this->get('router')->generate('kitpages_simpleedm_document_documentlist'));
        }

        return $this->render('KitpagesSimpleEdmBundle:Document:documentAdd.html.twig', array(
            'form'   => $form->createView()
        ));
    }

    public function documentEditAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SIMPLEEDM_WRITER')) {
            throw new AccessDeniedException();
        }
        $om = $this->getDoctrine()->getManager();
        $entity = $om->getRepository('KitpagesSimpleEdmBundle:Document')->find($id);

        $documentForm = new DocumentFormType();
        // build basic form
        $form   = $this->createForm($documentForm, $entity);
        $formHandler = $this->container->get('kitpages_simpleedm.document.form.handler');
        $process = $formHandler->process($form, $entity);

        if ($process) {
            return $this->redirect($this->get('router')->generate('kitpages_simpleedm_document_documentlist'));
        }

        return $this->render('KitpagesSimpleEdmBundle:Document:documentEdit.html.twig', array(
            'form'   => $form->createView(),
            'document' => $entity
        ));
    }

    public function documentListAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SIMPLEEDM_READER')) {
            throw new AccessDeniedException();
        }
        // create query builder
        $om = $this->get('doctrine')->getManager();
        $queryBuilder = $om->createQueryBuilder()
            ->select("document")
            ->from('KitpagesSimpleEdmBundle:Document', 'document')
            ->where('document.isActive = :active')
            ->setParameter('active', true)
            ;
        $gridConfig = new GridConfig();
        $gridConfig->setCountFieldName("document.id");
        $gridConfig->addField(new Field("document.reference", array("label" => "Reference", "filterable"=>true)));
        $gridConfig->addField(new Field("document.title", array("label" => "Title", "filterable"=>true)));
        $self = $this;
        $gridConfig->addField(
            new Field("document.fileOriginalName",
                array(
                    "label" => "File",
                    "filterable"=>true,
                    'formatValueCallback' => function($value, $row) use ($self) {
                        if ($value != null) { return '<a class="btn-standard kitpages-simpleedm-action-download" href="'.$self->generateUrl('kitpages_simpleedm_document_renderfile', array('id'=>$row["id"])).'">'.$value.'</a>';}
                        return 'No file';
                    },
                    'autoEscape' => false
                )
            ));
        $gridManager = $this->get("kitpages_data_grid.manager");
        $grid = $gridManager->getGrid($queryBuilder, $gridConfig, $this->getRequest());

        return $this->render('KitpagesSimpleEdmBundle:Document:documentList.html.twig', array(
            "grid" => $grid
        ));
    }

    public function documentDeleteAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SIMPLEEDM_WRITER')) {
            throw new AccessDeniedException();
        }
        $om = $this->get('doctrine')->getManager();
        $document = $om->getRepository('KitpagesSimpleEdmBundle:Document')->find($id);
        $om->remove($document);
        $om->flush();

        $target = $this->getRequest()->query->get('kitpages_target', null);

        return $this->redirect($target);
    }

    public function documentDisabledAction($id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SIMPLEEDM_WRITER')) {
            throw new AccessDeniedException();
        }
        $om = $this->get('doctrine')->getManager();
        $document = $om->getRepository('KitpagesSimpleEdmBundle:Document')->find($id);
        $document->disabled();
        $om->persist($document);
        $om->flush();

        $target = $this->getRequest()->query->get('kitpages_target', null);

        return $this->redirect($target);
    }

    public function renderFileAction($id){

        if (false === $this->get('security.context')->isGranted('ROLE_SIMPLEEDM_READER')) {
            throw new AccessDeniedException();
        }
        ini_set('memory_limit', '512M');
        if (!is_null($id)) {
            $om = $this->getDoctrine()->getManager();
            $document = $om->getRepository('KitpagesSimpleEdmBundle:Document')->find($id);

            // throw on event
            $event = new DocumentEvent();
            $event->setDocument($document);
            $this->get('event_dispatcher')->dispatch(KitpagesSimpleEdmEvents::onSendFileToBrowser, $event);

            // preventable action
            if (!$event->isDefaultPrevented()) {
                $fileSystem = $this->get('kitpages_simpleedm.documentListener')->getFileSystem();
                if ($document != null) {
                    $file = new AdapterFile($document->getFilePath(), true, $document->getMimeType());
                    if ($fileSystem->isFile($file)) {
                        $fileSystem->sendFileToBrowser(
                            $file,
                            $document->getFileOriginalName()
                        );
                    }
                }
            }
            // throw after event
            $this->get('event_dispatcher')->dispatch(KitpagesSimpleEdmEvents::afterSendFileToBrowser, $event);

        }
        return new Response(null);
    }

}
