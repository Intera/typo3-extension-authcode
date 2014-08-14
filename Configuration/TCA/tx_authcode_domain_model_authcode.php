<?php
$languagePrefix = 'LLL:EXT:authcode/Resources/Private/Language/locallang_db.xlf:';
$languagePrefixColumn = $languagePrefix . 'tx_authcode_domain_model_authcode.';

return array(

	'ctrl' => array(
		'title' => $languagePrefix . 'tx_authcode_domain_model_authcode',
		'label' => 'auth_code',
		'tstamp' => 'tstamp',
		'type' => 'type',
		'rootLevel' => 1,
		'enablecolumns' => array(
			'endtime' => 'valid_until',
		),
	),

	'interface' => array(
		'showRecordFieldList' => 'action, auth_code, identifier, identifier_context, reference_table, reference_table_hidden_field, reference_table_uid, reference_table_uid_field, serialized_auth_data, type, valid_until'
	),

	'columns' => array(

		'action' => array(
			'label' => $languagePrefixColumn . 'action',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						$languagePrefixColumn . 'action.I.' . \Tx\Authcode\Domain\Enumeration\AuthCodeAction::ENABLE_RECORD,
						\Tx\Authcode\Domain\Enumeration\AuthCodeAction::ENABLE_RECORD
					),
					array(
						$languagePrefixColumn . 'action.I.' . \Tx\Authcode\Domain\Enumeration\AuthCodeAction::ACCESS_PAGE,
						\Tx\Authcode\Domain\Enumeration\AuthCodeAction::ACCESS_PAGE
					),
				),
			)
		),

		'auth_code' => array(
			'label' => $languagePrefixColumn . 'auth_code',
			'config' => array(
				'type' => 'input',
			)
		),

		'identifier' => array(
			'label' => $languagePrefixColumn . 'identifier',
			'config' => array(
				'type' => 'input',
			)
		),

		'identifier_context' => array(
			'label' => $languagePrefixColumn . 'identifier_context',
			'config' => array(
				'type' => 'input',
			)
		),

		'reference_table' => array(
			'label' => $languagePrefixColumn . 'reference_table',
			'config' => array(
				'type' => 'input',
			)
		),

		'reference_table_hidden_field' => array(
			'label' => $languagePrefixColumn . 'reference_table_hidden_field',
			'config' => array(
				'type' => 'input',
			)
		),

		'reference_table_hidden_field_must_be_true' => array(
			'label' => $languagePrefixColumn . 'reference_table_hidden_field_must_be_true',
			'config' => array(
				'type' => 'check',
			)
		),

		'reference_table_uid' => array(
			'label' => $languagePrefixColumn . 'reference_table_uid',
			'config' => array(
				'type' => 'input',
			)
		),

		'reference_table_uid_field' => array(
			'label' => $languagePrefixColumn . 'reference_table_uid_field',
			'config' => array(
				'type' => 'input',
			)
		),

		'serialized_auth_data' => array(
			'label' => $languagePrefixColumn . 'serialized_auth_data',
			'config' => array(
				'type' => 'text',
			)
		),

		'type' => array(
			'label' => $languagePrefixColumn . 'type',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array(
						$languagePrefixColumn . 'type.I.' . \Tx\Authcode\Domain\Enumeration\AuthCodeType::RECORD,
						\Tx\Authcode\Domain\Enumeration\AuthCodeType::RECORD
					),
					array(
						$languagePrefixColumn . 'type.I.' . \Tx\Authcode\Domain\Enumeration\AuthCodeType::INDEPENDENT,
						\Tx\Authcode\Domain\Enumeration\AuthCodeType::INDEPENDENT
					),
				),
			)
		),

		'valid_until' => array(
			'label' => $languagePrefixColumn . 'valid_until',
			'config' => array(
				'type' => 'input',
				'eval' => 'datetime',
			)
		),
	),
	'types' => array(

		'0' => array(
			'showitem' => '
				type'
		),

		\Tx\Authcode\Domain\Enumeration\AuthCodeType::INDEPENDENT => array(
			'showitem' => '
				type, auth_code, valid_until,
				identifier, identifier_context,
				--div--;' . $languagePrefixColumn . 'tabs.additional_data, serialized_auth_data'
		),

		\Tx\Authcode\Domain\Enumeration\AuthCodeType::RECORD => array(
			'showitem' => '
				type, auth_code, valid_until,
				reference_table, reference_table_uid_field, reference_table_uid,
				reference_table_hidden_field, reference_table_hidden_field_must_be_true,
				--div--;' . $languagePrefixColumn . 'tabs.additional_data, serialized_auth_data'
		),
	),
	'palettes' => array()
);
