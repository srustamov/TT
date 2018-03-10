<?php



return array(

    'cache_path'=> storage_dir('cache/views'),

    'extensions' => array(
        App\Views\Edge\Extension::class,
    ),
    'minify' => true
);
