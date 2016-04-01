<?php

require_once __DIR__ . '/../vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(function ($class) {

    if (strpos($class, 'PSX\\Schema\\Parser\\Popo\\Annotation') === 0) {
        spl_autoload_call($class);

        return class_exists($class, false);
    }

});

