<?php

namespace Tx\Authcode;

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
use Tx\Authcode\Domain\Model\AuthCode;
use Tx\Authcode\Domain\Repository\AuthCodeRecordRepository;
use Tx\Authcode\Domain\Repository\AuthCodeRepository;
use Tx\Authcode\Domain\Repository\AuthCodeSessionRepository;
use Tx\Authcode\Exception\InvalidAuthCodeException;

/**
 * Provides methods for validating or invalidating auth codes.
 */
class AuthCodeValidator
{
    protected $authCodeIsOptional = false;

    /**
     * @var AuthCodeRecordRepository
     */
    protected $authCodeRecordRepository;

    /**
     * @var AuthCodeRepository
     */
    protected $authCodeRepository;

    /**
     * @var AuthCodeSessionRepository
     */
    protected $authCodeSessionRepository;

    /**
     * @var bool
     */
    protected $forceRecordDeletion = false;

    /**
     * @var bool
     */
    protected $invalidateAuthCodeAfterAccess = true;

    /**
     * @var bool
     */
    protected $updateTimestampOnActivation = true;

    public function injectAuthCodeRecordRepository(AuthCodeRecordRepository $authCodeRecordRepository)
    {
        $this->authCodeRecordRepository = $authCodeRecordRepository;
    }

    public function injectAuthCodeRepository(AuthCodeRepository $authCodeRepository)
    {
        $this->authCodeRepository = $authCodeRepository;
    }

    public function injectAuthCodeSessionRepository(AuthCodeSessionRepository $authCodeSessionRepository)
    {
        $this->authCodeSessionRepository = $authCodeSessionRepository;
    }

    /**
     * Invalidates the submitted auth code
     *
     * @param AuthCode $authCode
     */
    public function invalidateAuthCode($authCode)
    {
        $this->authCodeSessionRepository->clearAuthCodeFromSession();
        $this->authCodeRepository->clearAssociatedAuthCodes($authCode);
    }

    /**
     * @param boolean $authCodeIsOptional
     */
    public function setAuthCodeIsOptional($authCodeIsOptional)
    {
        $this->authCodeIsOptional = (bool)$authCodeIsOptional;
    }

    /**
     * @param boolean $forceRecordDeletion
     */
    public function setForceRecordDeletion($forceRecordDeletion)
    {
        $this->forceRecordDeletion = $forceRecordDeletion;
    }

    /**
     * @param bool $invalidateAuthCodeAfterAccess
     */
    public function setInvalidateAuthCodeAfterAccess($invalidateAuthCodeAfterAccess)
    {
        $this->invalidateAuthCodeAfterAccess = (bool)$invalidateAuthCodeAfterAccess;
    }

    /**
     * @param boolean $updateTimestampOnActivation
     */
    public function setUpdateTimestampOnActivation($updateTimestampOnActivation)
    {
        $this->updateTimestampOnActivation = $updateTimestampOnActivation;
    }

    /**
     * Checks the submitted auth code, executes the configured action and optionally
     * redirects the user to a success page if the auth code is valid.
     *
     * If the auth code is invalid an exception will be thrown or the user will be
     * redirected to a configured error page.
     *
     * @param AuthCode|string|NULL $authCode The submitted auth code GET parameter, an auth
     *     code instance from the repository or NULL. If NULL, the auth code will be read from GET or the session.
     * @return AuthCode
     * @throws Exception\InvalidAuthCodeException
     */
    public function validateAuthCodeAndExecuteAction($authCode = null)
    {

        if (!isset($authCode)) {
            $authCode = $this->authCodeRepository->getSubmittedAuthCode();
        } elseif (is_string($authCode)) {
            $authCode = $this->authCodeRepository->findOneByAuthCode($authCode);
        }

        if (!isset($authCode) && !$this->authCodeIsOptional) {
            throw new InvalidAuthCodeException();
        }

        switch ($authCode->getType()) {

            // For independent records we do not need to load the auth code record data.
            case AuthCodeType::INDEPENDENT:
                break;

            // For record auth codes we check the action that should be executed for the record.
            case AuthCodeType::RECORD:

                switch ($authCode->getAction()) {

                    // If action is enable record we unhide / enable the associated record.
                    case AuthCodeAction::RECORD_ENABLE:
                        $this->authCodeRecordRepository->enableAssociatedRecord(
                            $authCode,
                            $this->updateTimestampOnActivation
                        );
                        break;

                    // If action is delete record we delete the record.
                    case AuthCodeAction::RECORD_DELETE:
                        $this->authCodeRecordRepository->removeAssociatedRecord($authCode, $this->forceRecordDeletion);
                        break;

                    // For page access we do nothing
                    case AuthCodeAction::ACCESS_PAGE:
                        break;
                }
                break;
        }

        if ($this->invalidateAuthCodeAfterAccess) {
            $this->invalidateAuthCode($authCode);
        } else {
            // Store the authCode in the session so that the user can use it
            // on different pages without the need to append it as a get
            // parameter everytime
            $this->authCodeSessionRepository->storeAuthCodeInSession($authCode);
        }

        return $authCode;
    }
}
