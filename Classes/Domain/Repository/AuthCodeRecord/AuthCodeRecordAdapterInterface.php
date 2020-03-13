<?php

namespace Tx\Authcode\Domain\Repository\AuthCodeRecord;

use Tx\Authcode\Domain\Model\AuthCode;

interface AuthCodeRecordAdapterInterface
{
    /**
     * Enables the record, that is referenced by the submitted auth code
     *
     * @param AuthCode $authCode
     * @param bool $updateTimestamp
     */
    public function enableAssociatedRecord(AuthCode $authCode, $updateTimestamp);

    /**
     * Reads the data of the record that is referenced by the auth code
     * from the database
     *
     * @param AuthCode $authCode
     * @return NULL|array NULL if no data was found, otherwise an associative array of the record data
     */
    public function getAuthCodeRecordFromDB(AuthCode $authCode);

    /**
     * Deletes the records that is referenced by the auth code from the database.
     *
     * @param AuthCode $authCode
     * @param bool $forceDeletion If this is TRUE the record will be deleted from the database even if the has a
     *     "delete" field configured in the TCA.
     */
    public function removeAssociatedRecord(AuthCode $authCode, $forceDeletion = false);
}
