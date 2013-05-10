<?php

namespace Kitpages\SimpleEdmBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            "reference",
            "text",
            array(
            )
        );
        $builder->add(
            "title",
            "text",
            array(
                "label" => "Titre",
            )
        );
        $builder->add(
            "description",
            "textarea",
            array(
                "label" => "Description",
                'required' => false
            )
        );
        $builder->add(
            "isActive",
            "choice",
            array(
                "label" => "Active",
                "choices" => array(
                    '1' => 'True',
                    '0' => 'False'
                ),
                "expanded" => false,
                "multiple" => false
            )
        );
        $builder->add(
            'file',
            'file',
            array(
                "label" => "File",
                'required' => false
            )
        );

    }

    public function getName()
    {
        return 'kitpages_simpleedmbundle_documentform';
    }
}
