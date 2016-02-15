<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "authcode".
 *
 * Auto generated 04-07-2013 17:03
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Auth code libraries',
	'description' => 'Library for generating and validating one time authorization codes (e.g. for email validation).',
	'category' => 'misc',
	'version' => '0.2.1',
	'state' => 'beta',
	'author' => 'Alexander Stehlik',
	'author_email' => 'astehlik@intera.de',
	'author_company' => 'Intera GmbH',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.2-7.6.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);