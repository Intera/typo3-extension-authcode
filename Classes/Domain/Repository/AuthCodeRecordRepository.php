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
use Tx\Authcode\Domain\Repository\AuthCodeRecord\AuthCodeRecordAdapterInterface;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * A class providing helper functions for database records associated to auth codes.
 */
class AuthCodeRecordRepository implements SingletonInterface
{
    private $adapter;

    public function __construct(AuthCodeRecordAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Enables the record, that is referenced by the submitted auth code
     *
     * @param AuthCode $authCode
     * @param bool $updateTimestamp
     */
    public function enableAssociatedRecord(AuthCode $authCode, $updateTimestamp)
    {
        $this->adapter->enableAssociatedRecord($authCode, $updateTimestamp);
    }

    /**
     * Reads the data of the record that is referenced by the auth code
     * from the database
     *
     * @param AuthCode $authCode
     * @return NULL|array NULL if no data was found, otherwise an associative array of the record data
     */
    public function getAuthCodeRecordFromDB(AuthCode $authCode)
    {
        return $this->adapter->getAuthCodeRecordFromDB($authCode);
    }

    /**
     * Deletes the records that is referenced by the auth code from the database.
     *
     * @param AuthCode $authCode
     * @param bool $forceDeletion If this is TRUE the record will be deleted from the database even if the has a
     *     "delete" field configured in the TCA.
     */
    public function removeAssociatedRecord(AuthCode $authCode, $forceDeletion = false)
    {
        $this->adapter->removeAssociatedRecord($authCode, $forceDeletion);
    }
}
