<?php
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace Tx\Authcode\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "authcode".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use ArrayAccess;
use DateTime;
use RuntimeException;
use Tx\Authcode\Domain\Enumeration\AuthCodeAction;
use Tx\Authcode\Domain\Enumeration\AuthCodeType;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * An authcode record.
 */
class AuthCode extends AbstractEntity implements ArrayAccess
{
    /**
     * @var \Tx\Authcode\Domain\Enumeration\AuthCodeAction
     */
    protected $action;

    /**
     * @var array
     */
    protected $arrayKeyMethodMapping = [
        'uid' => 'Uid',
        'pid' => 'Pid',
        'tstamp' => 'LegacyTstamp',
        'reference_table' => 'ReferenceTable',
        'reference_table_uid_field' => 'ReferenceTableUidField',
        'reference_table_uid' => 'ReferenceTableUid',
        'auth_code' => 'AuthCode',
        'reference_table_hidden_field' => 'ReferenceTableHiddenField',
        'serialized_auth_data' => 'AdditionalData',
        'action' => 'Action',
        'identifier' => 'Identifier',
        'identifier_context' => 'IdentifierContext',
        'type' => 'Type',
    ];

    /**
     * @var string
     */
    protected $authCode;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $identifierContext;

    /**
     * @var string
     */
    protected $referenceTable;

    /**
     * @var string
     */
    protected $referenceTableHiddenField;

    /**
     * @var boolean
     */
    protected $referenceTableHiddenFieldMustBeTrue;

    /**
     * @var int
     */
    protected $referenceTableUid;

    /**
     * @var string
     */
    protected $referenceTableUidField = 'uid';

    /**
     * @var string
     */
    protected $serializedAuthData;

    /**
     * @var int
     */
    protected $tstamp;

    /**
     * @var \Tx\Authcode\Domain\Enumeration\AuthCodeType
     */
    protected $type;

    /**
     * @var DateTime
     */
    protected $validUntil;

    public function getAction()
    {
        return (string)$this->action;
    }

    public function getAdditionalData()
    {
        $additionalData = trim($this->serializedAuthData);
        if ($additionalData !== '') {
            $additionalData = unserialize($additionalData);
            if (!is_array($additionalData)) {
                throw new RuntimeException(
                    'The additional data stored in the auth code can not be unserialized to an array.'
                );
            }
        } else {
            $additionalData = [];
        }

        return $additionalData;
    }

    public function getAuthCode()
    {
        return $this->authCode;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getIdentifierContext()
    {
        return $this->identifierContext;
    }

    public function getReferenceTable()
    {
        return $this->referenceTable;
    }

    public function getReferenceTableHiddenField()
    {
        return (string)$this->referenceTableHiddenField;
    }

    public function getReferenceTableHiddenFieldMustBeTrue()
    {
        return $this->referenceTableHiddenFieldMustBeTrue;
    }

    public function getReferenceTableUid()
    {
        return $this->referenceTableUid;
    }

    public function getReferenceTableUidField()
    {
        return $this->referenceTableUidField;
    }

    public function getType()
    {
        return (string)$this->type;
    }

    /**
     * @param mixed $offset
     * @return bool
     * @deprecated Using ArrayAccess for auth code records is deprecated since 0.2.0 and will be removed in 0.4.0. Use
     *     the matching getter / setters instead.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->arrayKeyMethodMapping);
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @deprecated Using ArrayAccess for auth code records is deprecated since 0.2.0 and will be removed in 0.4.0. Use
     *     the matching getter / setters instead.
     */
    public function offsetGet($offset)
    {
        if (!array_key_exists($offset, $this->arrayKeyMethodMapping)) {
            return null;
        }
        $getter = 'get' . $this->arrayKeyMethodMapping[$offset];
        return $this->$getter();
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @deprecated Using ArrayAccess for auth code records is deprecated since 0.2.0 and will be removed in 0.4.0. Use
     *     the matching getter / setters instead.
     */
    public function offsetSet($offset, $value)
    {
        if (!array_key_exists($offset, $this->arrayKeyMethodMapping)) {
            return;
        }
        $setter = 'set' . $this->arrayKeyMethodMapping;
        $this->$setter($value);
    }

    /**
     * @param mixed $offset
     * @return void
     * @deprecated Using ArrayAccess for auth code records is deprecated since 0.2.0 and will be removed in 0.4.0. Use
     *     the matching getter / setters instead.
     */
    public function offsetUnset($offset)
    {
        if (!array_key_exists($offset, $this->arrayKeyMethodMapping)) {
            return;
        }
        $setter = 'set' . $this->arrayKeyMethodMapping;
        $this->$setter(null);
    }

    public function setAction($action)
    {
        $this->action = new AuthCodeAction($action);
    }

    public function setAdditionalData(array $additionalData)
    {
        $this->serializedAuthData = serialize($additionalData);
    }

    public function setAuthCode($authCode)
    {
        $this->authCode = $authCode;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function setIdentifierContext($identifierContext)
    {
        $this->identifierContext = $identifierContext;
    }

    public function setReferenceTable($referenceTable)
    {
        $this->referenceTable = $referenceTable;
    }

    public function setReferenceTableHiddenField($referenceTableHiddenField)
    {
        $this->referenceTableHiddenField = $referenceTableHiddenField;
    }

    public function setReferenceTableHiddenFieldMustBeTrue($referenceTableHiddenFieldMustBeTrue)
    {
        $this->referenceTableHiddenFieldMustBeTrue = $referenceTableHiddenFieldMustBeTrue;
    }

    public function setReferenceTableUid($referenceTableUid)
    {
        $this->referenceTableUid = $referenceTableUid;
    }

    public function setReferenceTableUidField($referenceTableUidField)
    {
        $this->referenceTableUidField = $referenceTableUidField;
    }

    public function setType($type)
    {
        $this->type = new AuthCodeType($type);
    }

    public function setValidUntil(DateTime $validUntil)
    {
        $this->validUntil = $validUntil;
    }

    /**
     * The timestamp when this authcode was created.
     *
     * @deprecated This was used to calculate the expire date in the past and is replaced by the validUntil property.
     */
    protected function getLegacyTstamp()
    {
        return $this->tstamp;
    }
}
