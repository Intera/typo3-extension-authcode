<?php
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

declare(strict_types=1);

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

    public function getAction(): string
    {
        return (string)$this->action;
    }

    public function getAdditionalData(): array
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

    public function getAuthCode(): string
    {
        return $this->authCode;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getIdentifierContext(): string
    {
        return $this->identifierContext;
    }

    public function getReferenceTable(): string
    {
        return $this->referenceTable;
    }

    public function getReferenceTableHiddenField(): string
    {
        return $this->referenceTableHiddenField;
    }

    public function getReferenceTableHiddenFieldMustBeTrue(): bool
    {
        return $this->referenceTableHiddenFieldMustBeTrue;
    }

    public function getReferenceTableUid(): int
    {
        return $this->referenceTableUid;
    }

    public function getReferenceTableUidField(): string
    {
        return $this->referenceTableUidField;
    }

    public function getType(): string
    {
        return (string)$this->type;
    }

    /**
     * @param string $offset
     * @return bool
     * @deprecated Using ArrayAccess for auth code records is deprecated since 0.2.0 and will be removed in 0.4.0. Use
     *     the matching getter / setters instead.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->arrayKeyMethodMapping);
    }

    /**
     * @param string $offset
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
     * @param string $offset
     * @param string $value
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
     * @param string $offset
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

    public function setAction(string $action): void
    {
        $this->action = new AuthCodeAction($action);
    }

    public function setAdditionalData(array $additionalData): void
    {
        $this->serializedAuthData = serialize($additionalData);
    }

    public function setAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function setIdentifierContext(string $identifierContext): void
    {
        $this->identifierContext = $identifierContext;
    }

    public function setReferenceTable(string $referenceTable): void
    {
        $this->referenceTable = $referenceTable;
    }

    public function setReferenceTableHiddenField(string $referenceTableHiddenField): void
    {
        $this->referenceTableHiddenField = $referenceTableHiddenField;
    }

    public function setReferenceTableHiddenFieldMustBeTrue(bool $referenceTableHiddenFieldMustBeTrue): void
    {
        $this->referenceTableHiddenFieldMustBeTrue = $referenceTableHiddenFieldMustBeTrue;
    }

    public function setReferenceTableUid(int $referenceTableUid): void
    {
        $this->referenceTableUid = $referenceTableUid;
    }

    public function setReferenceTableUidField(string $referenceTableUidField): void
    {
        $this->referenceTableUidField = $referenceTableUidField;
    }

    public function setType(string $type): void
    {
        $this->type = new AuthCodeType($type);
    }

    public function setValidUntil(DateTime $validUntil): void
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
