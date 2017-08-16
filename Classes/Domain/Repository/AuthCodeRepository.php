<?php

namespace Tx\Authcode\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "authcode".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Tx\Authcode\Domain\Enumeration\AuthCodeAction;
use Tx\Authcode\Domain\Enumeration\AuthCodeType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * A class providing helper functions for auth codes stored in the database
 */
class AuthCodeRepository extends Repository {
	/**
	 * This string is parsed by strtotime and specifies
	 * the timestamp when the auth codes are expired
	 *
	 * @var string
	 */
	protected $authCodeExpiryTime = '+ 1 day';

	/**
	 * @var \Tx\Authcode\Domain\Repository\AuthCodeSessionRepository
	 */
	protected $authCodeSessionRepository;

	/**
	 * If this is true all auth codes belonging to the same record will be cleared
	 * before a new auth code for this record is generated.
	 *
	 * @var bool
	 */
	protected $autoClearAssociatedCodes = TRUE;

	/**
	 * If this is true every time an auth code is read from the
	 * database expired auth codes will be deleted from the database
	 *
	 * @var bool
	 */
	protected $autoDeleteExpiredAuthCodes = TRUE;

	public function injectAuthCodeSessionRepository(AuthCodeSessionRepository $authCodeSessionRepository) {
		$this->authCodeSessionRepository = $authCodeSessionRepository;
	}

	/**
	 * Removes all auth codes that reference the same record as the given auth code.
	 *
	 * @param \Tx\Authcode\Domain\Model\AuthCode $authCode
	 * @throws \InvalidArgumentException
	 */
	public function clearAssociatedAuthCodes($authCode) {

		if ($authCode->getType() === AuthCodeType::RECORD) {

			$this->clearRecordAuthCodes(
				$authCode->getReferenceTable(),
				$authCode->getReferenceTableUid(),
				$authCode->getReferenceTableUidField()
			);
		} else {

			$this->clearIndependentAuthCodes(
				$authCode->getIdentifier(),
				$authCode->getIdentifierContext()
			);
		}

		$this->persistenceManager->persistAll();
	}

	/**
	 * Creates a query that ignores the current storage page.
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function createQuery() {
		$query = parent::createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		return $query;
	}

	/**
	 * Removes all auth codes from the database where validUntil
	 * is older than the current timestamp.
	 */
	public function deleteExpiredAuthCodesFromDatabase() {

		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		$query->matching(
			$query->lessThan('validUntil', $GLOBALS['EXEC_TIME'])
		);

		$authCodes = $query->execute();
		foreach ($authCodes as $authCode) {
			$this->remove($authCode);
		}

		$this->persistenceManager->persistAll();
	}

	/**
	 * Retrieves the data of the given auth code from the database. Before
	 * executing the query to get the auth code data expired auth codes
	 * are deleted from the database if this is not disabled in the settings.
	 *
	 * @param string $authCode the submitted auth code
	 * @return \Tx\Authcode\Domain\Model\AuthCode|NULL NULL if no data was found, otherwise the matching auth code
	 *     record.
	 */
	public function findOneByAuthCode($authCode) {

		if ($this->autoDeleteExpiredAuthCodes) {
			$this->deleteExpiredAuthCodesFromDatabase();
		}

		$query = $this->createQuery();
		$query->matching(
			$query->equals('authCode', $authCode)
		);

		/** @var \Tx\Authcode\Domain\Model\AuthCode $authCode */
		$authCode = $query->execute()->getFirst();
		return $authCode;
	}

	/**
	 * Searches for an independent auth code with the given auth code in the given context.
	 *
	 * @param string $authCode the submitted auth code.
	 * @param string $context The auth code context.
	 * @return \Tx\Authcode\Domain\Model\AuthCode|NULL NULL if no data was found, otherwise the matching auth code
	 *     record.
	 */
	public function findOneIndependentByAuthCodeAndContext($authCode, $context) {

		if ($this->autoDeleteExpiredAuthCodes) {
			$this->deleteExpiredAuthCodesFromDatabase();
		}

		$query = $this->createQuery();
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$query->matching(
			$query->logicalAnd(
				$query->equals('authCode', $authCode),
				$query->equals('type', AuthCodeType::INDEPENDENT),
				$query->equals('identifierContext', $context)
			)
		);

		/** @var \Tx\Authcode\Domain\Model\AuthCode $authCode */
		$authCode = $query->execute()->getFirst();
		return $authCode;
	}

	/**
	 * Generates an auth code for accessing a form that is independent from
	 * any table records but only needs an identifier and a context name for that
	 * identifier.
	 *
	 * The identifier should be unique in the given context.
	 *
	 * @param \Tx\Authcode\Domain\Model\AuthCode $authCode
	 * @param string $identifier
	 * @param string $context
	 */
	public function generateIndependentAuthCode($authCode, $identifier, $context) {
		$authCode->setIdentifier($identifier);
		$authCode->setIdentifierContext($context);

		// Action is not relevant for independent auth codes but we set it to a valid value.
		$authCode->setAction(AuthCodeAction::ACCESS_PAGE);

		$this->initializeAuthCode($authCode, AuthCodeType::INDEPENDENT);

		if ($this->autoClearAssociatedCodes) {
			$this->clearAssociatedAuthCodes($authCode);
		}

		$this->add($authCode);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Generates a new auth code based on the given row data and clears
	 * all other auth codes that reference the same row
	 *
	 * @param \Tx\Authcode\Domain\Model\AuthCode $authCode
	 * @param string $action
	 * @param string $table
	 * @param int $uid
	 * @throws \InvalidArgumentException
	 */
	public function generateRecordAuthCode($authCode, $action, $table, $uid) {
		$authCode->setReferenceTable($table);
		$authCode->setReferenceTableUid($uid);
		$authCode->setAction($action);

		if ($action === AuthCodeAction::RECORD_ENABLE) {
			$hiddenField = (string)$authCode->getReferenceTableHiddenField();
			if ($hiddenField === '') {
				if (
					isset($GLOBALS['TCA'][$authCode->getReferenceTable()]['ctrl']['enablecolumns']['disabled'])
					&& $GLOBALS['TCA'][$authCode->getReferenceTable()]['ctrl']['enablecolumns']['disabled'] !== ''
				) {
					$authCode->setReferenceTableHiddenField(
						$GLOBALS['TCA'][$authCode->getReferenceTable()]['ctrl']['enablecolumns']['disabled']
					);
				} else {
					throw new \InvalidArgumentException(
						'Hidden field is not set in auth code record and can not be found in TCA.'
					);
				}
			}
		}

		$this->initializeAuthCode($authCode, AuthCodeType::RECORD);

		if ($this->autoClearAssociatedCodes) {
			$this->clearAssociatedAuthCodes($authCode);
		}

		$this->add($authCode);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Tries to read the auth code from the GET/POST data array or from the session.
	 *
	 * @param string $variableName
	 * @param string $formValuesPrefix
	 * @return \Tx\Authcode\Domain\Model\AuthCode
	 */
	public function getSubmittedAuthCode($variableName = 'authCode', $formValuesPrefix = '') {
		$authCode = '';

		$formValuesPrefix = trim($formValuesPrefix);
		if ($formValuesPrefix === '') {
			$authCode = GeneralUtility::_GP($variableName);
		} else {
			$gpArray = GeneralUtility::_GP($formValuesPrefix);
			if (is_array($gpArray) && array_key_exists($variableName, $gpArray)) {
				$authCode = $gpArray[$variableName];
			}
		}

		$authCode = trim($authCode);
		if ($authCode === '') {
			$authCode = $this->authCodeSessionRepository->getAuthCodeFromSession();
		}

		return $this->findOneByAuthCode($authCode);
	}

	/**
	 * Returns the DateTime until the generated auth codes should be valid.
	 *
	 * @return \DateTime
	 */
	public function getValidUntil() {
		$this->validateAuthCodeExpiryTime();
		return new \DateTime($this->authCodeExpiryTime);
	}

	/**
	 * Updates the timestamp of the auth code so that the expiration time is moved in the future.
	 *
	 * @param \Tx\Authcode\Domain\Model\AuthCode $authCode
	 */
	public function refreshAuthCode($authCode) {
		$authCode->setValidUntil($this->getValidUntil());
		$this->update($authCode);
		$this->persistenceManager->persistAll();
	}

	/**
	 * Sets a new auth code expiry time, if you want to use it you have
	 * to call it before running getAuthCodeDataFromDBI() or
	 * deleteExpiredAuthCodesFromDatabase()
	 *
	 * @param string $authCodeExpiryTime Time that will be parsed with strtotime
	 * @throws \Exception if string can not be parsed
	 */
	public function setAuthCodeExpiryTime($authCodeExpiryTime) {
		$this->authCodeExpiryTime = $authCodeExpiryTime;
		$this->validateAuthCodeExpiryTime();
	}

	/**
	 * @param boolean $autoClearAssociatedCodes
	 */
	public function setAutoClearAssociatedCodes($autoClearAssociatedCodes) {
		$this->autoClearAssociatedCodes = $autoClearAssociatedCodes;
	}

	/**
	 * If this is set to TRUE (default) expired auth codes will automatically be deleted
	 * every time an auth code is read from the database.
	 *
	 * @param bool $autoDeleteExpiredAuthCodes
	 */
	public function setAutoDeleteExpiredAuthCodes($autoDeleteExpiredAuthCodes) {
		$this->autoDeleteExpiredAuthCodes = (bool)$autoDeleteExpiredAuthCodes;
	}

	/**
	 * Clears all auth codes that match the given identifier for the given context
	 *
	 * @param string $identifier
	 * @param string $context
	 */
	protected function clearIndependentAuthCodes($identifier, $context) {
		$authCodes = $this->findIndependendByIdentifierAndContext($identifier, $context);
		foreach ($authCodes as $authCode) {
			$this->remove($authCode);
		}
	}

	/**
	 * Removes all auth codes that reference the given record
	 *
	 * @param $table string
	 * @param $uid string
	 * @param $uidField string
	 */
	protected function clearRecordAuthCodes($table, $uid, $uidField) {
		$authCodes = $this->findByReferencedRecord($table, $uid, $uidField);
		foreach ($authCodes as $authCode) {
			$this->remove($authCode);
		}
	}

	/**
	 * Finds all auth codes that reference the record with the given UID in the given table.
	 *
	 * @param string $table
	 * @param int $uid
	 * @param string $uidField
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	protected function findByReferencedRecord($table, $uid, $uidField) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$query->matching(
			$query->logicalAnd(
				$query->equals('type', AuthCodeType::RECORD),
				$query->equals('referenceTable', $table),
				$query->equals('referenceTableUidField', $uidField),
				$query->equals('referenceTableUid', (int)$uid)
			)
		);
		return $query->execute();
	}

	/**
	 * Finds all independent auth codes for the identifier in the given context.
	 *
	 * @param string $identifier
	 * @param string $context
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	protected function findIndependendByIdentifierAndContext($identifier, $context) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$query->matching(
			$query->logicalAnd(
				$query->equals('type', AuthCodeType::INDEPENDENT),
				$query->equals('identifier', $identifier),
				$query->equals('identifierContext', $context)
			)
		);
		return $query->execute();
	}

	/**
	 * Generates a random string and uses the given string as prefix.
	 * Finally a MD5 hash of the string will be generated.
	 *
	 * @param \Tx\Authcode\Domain\Model\AuthCode $authCode
	 * @param string $type
	 * @return void
	 */
	protected function initializeAuthCode($authCode, $type) {

		$authCodeString = GeneralUtility::getRandomHexString(16);
		$authCodeString = md5(serialize($authCode) . $authCodeString);
		$authCode->setAuthCode($authCodeString);

		$authCode->setType($type);
		$authCode->setValidUntil($this->getValidUntil());
	}

	/**
	 * Validates the current valid in $this->authCodeExpiryTime.
	 *
	 * @throws \Exception If expiry time is invalid.
	 */
	protected function validateAuthCodeExpiryTime() {

		$authCodeExpiryTimestamp = strtotime($this->authCodeExpiryTime);
		if ($authCodeExpiryTimestamp === FALSE) {
			throw new \Exception('An invalid auth code expiry time was provided: ' . $this->authCodeExpiryTime);
		}

		if ($authCodeExpiryTimestamp <= time()) {
			throw new \Exception('The auth code expiry time must be in the future: ' . $this->authCodeExpiryTime);
		}
	}
}
