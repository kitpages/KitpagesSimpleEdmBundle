<?php


namespace Kitpages\SimpleEdmBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\FormError;



class DocumentFormHandler
{
    protected $request;
    protected $doctrine;

    public function __construct(
        Registry $doctrine,
        Request $request
    )
    {
        $this->doctrine = $doctrine;
        $this->request = $request;
    }

    public function process(Form $form, $entity)
    {
        if ($this->request->getMethod() == 'POST' && $this->request->request->get($form->getName()) !== null) {
            $form->bind($this->request);

            if ($form->isValid()) {
                // modifies the entity to force registration, launch the PrePersist ...
                $file = $form['file']->getData();
                if (!empty($file)) {
                    $entity->setFileOriginalName('');
                }
                $om = $this->doctrine->getManager();
                $om->persist($entity);
                $om->flush();
                $this->request->getSession()->setFlash('notice', 'Your document is saved');
                return true;
            }
            $this->request->getSession()->setFlash('error', $this->getRenderErrorMessages($form));
        }
        return false;
    }

    private function getRenderErrorMessages(\Symfony\Component\Form\Form $form) {
        $errorFieldList = $this->getErrorMessages($form);
        $errorHtml = '<ul>';
        foreach($errorFieldList as $errorList) {
            if (is_array($errorList)) {
                foreach($errorList as $error) {
                    $errorHtml .= '<li>'.$error.'</li>';
                }
            } else {
                $errorHtml .= '<li>'.$errorList.'</li>';
            }
        }
        $errorHtml .= '</ul>';

        return $errorHtml;
    }

    private function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $errors[] = strtr($error->getMessageTemplate(), $error->getMessageParameters());
        }
        if ($form->count() > 0) {
            foreach ($form->all() as $child) {
//                if (!$child->isValid()) {
//                    $errors[$child->getName()] = $this->getErrorMessages($child);
//
//                }
            }
        }
        return $errors;
    }

}
