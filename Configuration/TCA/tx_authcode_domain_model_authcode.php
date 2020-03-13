<?php

use Tx\Authcode\Domain\Enumeration\AuthCodeAction;
use Tx\Authcode\Domain\Enumeration\AuthCodeType;

$languagePrefix = 'LLL:EXT:authcode/Resources/Private/Language/locallang_db.xlf:';
$languagePrefixColumn = $languagePrefix . 'tx_authcode_domain_model_authcode.';

return [

    'ctrl' => [
        'title' => $languagePrefix . 'tx_authcode_domain_model_authcode',
        'label' => 'auth_code',
        'tstamp' => 'tstamp',
        'type' => 'type',
        'rootLevel' => 1,
        'enablecolumns' => ['endtime' => 'valid_until'],
    ],

    'interface' => [
        'showRecordFieldList' => '
            action, auth_code, identifier, identifier_context, reference_table,
            reference_table_hidden_field, reference_table_uid, reference_table_uid_field,
            serialized_auth_data, type, valid_until
        ',
    ],

    'columns' => [

        'action' => [
            'label' => $languagePrefixColumn . 'action',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        $languagePrefixColumn . 'action.I.' . AuthCodeAction::RECORD_ENABLE,
                        AuthCodeAction::RECORD_ENABLE,
                    ],
                    [
                        $languagePrefixColumn . 'action.I.' . AuthCodeAction::RECORD_DELETE,
                        AuthCodeAction::RECORD_DELETE,
                    ],
                    [
                        $languagePrefixColumn . 'action.I.' . AuthCodeAction::ACCESS_PAGE,
                        AuthCodeAction::ACCESS_PAGE,
                    ],
                ],
            ],
        ],

        'auth_code' => [
            'label' => $languagePrefixColumn . 'auth_code',
            'config' => ['type' => 'input'],
        ],

        'identifier' => [
            'label' => $languagePrefixColumn . 'identifier',
            'config' => ['type' => 'input'],
        ],

        'identifier_context' => [
            'label' => $languagePrefixColumn . 'identifier_context',
            'config' => ['type' => 'input'],
        ],

        'reference_table' => [
            'label' => $languagePrefixColumn . 'reference_table',
            'config' => ['type' => 'input'],
        ],

        'reference_table_hidden_field' => [
            'label' => $languagePrefixColumn . 'reference_table_hidden_field',
            'config' => ['type' => 'input'],
        ],

        'reference_table_hidden_field_must_be_true' => [
            'label' => $languagePrefixColumn . 'reference_table_hidden_field_must_be_true',
            'config' => ['type' => 'check'],
        ],

        'reference_table_uid' => [
            'label' => $languagePrefixColumn . 'reference_table_uid',
            'config' => ['type' => 'input'],
        ],

        'reference_table_uid_field' => [
            'label' => $languagePrefixColumn . 'reference_table_uid_field',
            'config' => ['type' => 'input'],
        ],

        'serialized_auth_data' => [
            'label' => $languagePrefixColumn . 'serialized_auth_data',
            'config' => ['type' => 'text'],
        ],

        'tstamp' => [
            'config' => ['type' => 'passthrough'],
        ],

        'type' => [
            'label' => $languagePrefixColumn . 'type',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        $languagePrefixColumn . 'type.I.' . AuthCodeType::RECORD,
                        AuthCodeType::RECORD,
                    ],
                    [
                        $languagePrefixColumn . 'type.I.' . AuthCodeType::INDEPENDENT,
                        AuthCodeType::INDEPENDENT,
                    ],
                ],
            ],
        ],

        'valid_until' => [
            'label' => $languagePrefixColumn . 'valid_until',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
            ],
        ],
    ],
    'types' => [

        '0' => [
            'showitem' => '
				type',
        ],

        AuthCodeType::INDEPENDENT => [
            'showitem' => '
				type, auth_code, valid_until,
				identifier, identifier_context,
				--div--;' . $languagePrefixColumn . 'tabs.additional_data, serialized_auth_data',
        ],

        AuthCodeType::RECORD => [
            'showitem' => '
				type, auth_code, valid_until,
				reference_table, reference_table_uid_field, reference_table_uid,
				reference_table_hidden_field, reference_table_hidden_field_must_be_true,
				--div--;' . $languagePrefixColumn . 'tabs.additional_data, serialized_auth_data',
        ],
    ],
    'palettes' => [],
];
