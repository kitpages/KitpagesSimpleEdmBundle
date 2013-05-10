KitpagesSimpleEdm
=================

This is an electronic document manager for symfony.


Installation
============
use composer update

add the new Bundle in app/appKernel.php

You need to create a table in the database :
launch command:
php app/console doctrine:schema:update

Step1: AppKernel.php
Add the following entries to your autoloader:
        $bundles = array(
        ...
            new Kitpages\SimpleEdmBundle\KitpagesSimpleEdmBundle(),
            new Kitpages\FileSystemBundle\KitpagesFileSystemBundle(),
            new Kitpages\DataGridBundle\KitpagesDataGridBundle(),
        );


Configuration example kitpagesFileSystemBundle
============

kitpages_file_system:
    file_system_list:
        kitpagesSimpleEdm:
            local:
                directory_public: %kernel.root_dir%/../web
                directory_private: %kernel.root_dir%
                base_url: %base_url%
