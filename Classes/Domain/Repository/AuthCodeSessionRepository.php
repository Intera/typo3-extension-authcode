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

use Tx\Authcode\Domain\Model\AuthCode;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * A class providing helper functions for auth codes stored in the session.
 */
class AuthCodeSessionRepository implements SingletonInterface
{
    const SESSION_KEY = 'tx_authcode_authcode';

    /**
     * Removes the auth code from the session
     */
    public function clearAuthCodeFromSession()
    {
        unset($_SESSION[static::SESSION_KEY]);
    }

    /**
     * Tries to read the auth code from the session
     *
     * @return string
     */
    public function getAuthCodeFromSession()
    {
        return $_SESSION[static::SESSION_KEY];
    }

    /**
     * Stores the given auth code in the session
     *
     * @param AuthCode $authCode
     */
    public function storeAuthCodeInSession($authCode)
    {
        $_SESSION[static::SESSION_KEY] = $authCode->getAuthCode();
    }
}
