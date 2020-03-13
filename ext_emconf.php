<?php

/** @var string $_EXTKEY */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Auth code libraries',
    'description' => 'Library for generating and validating one time authorization codes (e.g. for email validation).',
    'category' => 'misc',
    'version' => '9.0.0-dev',
    'state' => 'beta',
    'author' => 'Alexander Stehlik',
    'author_email' => 'astehlik@intera.de',
    'author_company' => 'Intera GmbH',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
            'extbase' => '0.0.0-99.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
