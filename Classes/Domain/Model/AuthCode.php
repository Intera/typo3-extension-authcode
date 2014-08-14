<?php
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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * An authcode record.
 */
class AuthCode extends AbstractEntity {

	/**
	 * @var \Tx\Authcode\Domain\Enumeration\AuthCodeAction
	 */
	protected $action;

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
	 * @var \Tx\Authcode\Domain\Enumeration\AuthCodeType
	 */
	protected $type;

	/**
	 * @var \DateTime
	 */
	protected $validUntil;

	/**
	 * @return string
	 */
	public function getAction() {
		return (string)$this->action;
	}

	/**
	 * @return string
	 */
	public function getAuthCode() {
		return $this->authCode;
	}

	/**
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * @return string
	 */
	public function getIdentifierContext() {
		return $this->identifierContext;
	}

	/**
	 * @return string
	 */
	public function getReferenceTable() {
		return $this->referenceTable;
	}

	/**
	 * @return string
	 */
	public function getReferenceTableHiddenField() {
		return $this->referenceTableHiddenField;
	}

	/**
	 * @return boolean
	 */
	public function getReferenceTableHiddenFieldMustBeTrue() {
		return $this->referenceTableHiddenFieldMustBeTrue;
	}

	/**
	 * @return int
	 */
	public function getReferenceTableUid() {
		return $this->referenceTableUid;
	}

	/**
	 * @return string
	 */
	public function getReferenceTableUidField() {
		return $this->referenceTableUidField;
	}

	/**
	 * @return \Tx\Authcode\Domain\Enumeration\AuthCodeType
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action) {
		$this->action = new \Tx\Authcode\Domain\Enumeration\AuthCodeAction($action);
	}

	/**
	 * @param string $authCode
	 */
	public function setAuthCode($authCode) {
		$this->authCode = $authCode;
	}

	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * @param string $identifierContext
	 */
	public function setIdentifierContext($identifierContext) {
		$this->identifierContext = $identifierContext;
	}

	/**
	 * @param string $referenceTable
	 */
	public function setReferenceTable($referenceTable) {
		$this->referenceTable = $referenceTable;
	}

	/**
	 * @param string $referenceTableHiddenField
	 */
	public function setReferenceTableHiddenField($referenceTableHiddenField) {
		$this->referenceTableHiddenField = $referenceTableHiddenField;
	}

	/**
	 * @param boolean $referenceTableHiddenFieldMustBeTrue
	 */
	public function setReferenceTableHiddenFieldMustBeTrue($referenceTableHiddenFieldMustBeTrue) {
		$this->referenceTableHiddenFieldMustBeTrue = $referenceTableHiddenFieldMustBeTrue;
	}

	/**
	 * @param int $referenceTableUid
	 */
	public function setReferenceTableUid($referenceTableUid) {
		$this->referenceTableUid = $referenceTableUid;
	}

	/**
	 * @param string $referenceTableUidField
	 */
	public function setReferenceTableUidField($referenceTableUidField) {
		$this->referenceTableUidField = $referenceTableUidField;
	}

	/**
	 * @param string $serializedAuthData
	 */
	public function setSerializedAuthData($serializedAuthData) {
		$this->serializedAuthData = $serializedAuthData;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = new \Tx\Authcode\Domain\Enumeration\AuthCodeType($type);
	}

	/**
	 * @param \DateTime $validUntil
	 */
	public function setValidUntil($validUntil) {
		$this->validUntil = $validUntil;
	}
}