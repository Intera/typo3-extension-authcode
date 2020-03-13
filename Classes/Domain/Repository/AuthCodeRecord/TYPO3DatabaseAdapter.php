<?php

namespace Tx\Authcode\Domain\Repository\AuthCodeRecord;

use Exception;
use Tx\Authcode\Domain\Model\AuthCode;
use TYPO3\CMS\Core\Database\DatabaseConnection;

class TYPO3DatabaseAdapter implements AuthCodeRecordAdapterInterface
{
    /**
     * @var DatabaseConnection
     */
    protected $typo3Db;

    /**
     * Initializes required properties.
     */
    public function __construct()
    {
        $this->typo3Db = $GLOBALS['TYPO3_DB'];
    }

    /**
     * Enables the record, that is referenced by the submitted auth code
     *
     * @param AuthCode $authCode
     * @param bool $updateTimestamp
     * @throws Exception
     */
    public function enableAssociatedRecord(AuthCode $authCode, $updateTimestamp)
    {
        $updateTable = $authCode->getReferenceTable();
        $uidField = $authCode->getReferenceTableUidField();
        $uid = $authCode->getReferenceTableUid();
        $hiddenField = $authCode->getReferenceTableHiddenField();

        $updateArray = [
            $hiddenField => $authCode->getReferenceTableHiddenFieldMustBeTrue() ? 1 : 0,
        ];

        if ($updateTimestamp
            && isset($GLOBALS['TCA'][$updateTable]['ctrl']['tstamp'])
            && !empty($GLOBALS['TCA'][$updateTable]['ctrl']['tstamp'])
        ) {
            $tstampField = $GLOBALS['TCA'][$updateTable]['ctrl']['tstamp'];
            $updateArray[$tstampField] = $GLOBALS['EXEC_TIME'];
        }

        $enableQuery = $this->typo3Db->UPDATEquery($updateTable, $uidField . '=' . $uid, $updateArray);

        $enableResult = $this->typo3Db->sql_query($enableQuery);
        if (!$enableResult) {
            throw new Exception(
                'SQL error when enabling the record from the authCode: ' . $this->typo3Db->sql_error()
            );
        }
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
        $authCodeRecord = null;

        $table = $authCode->getReferenceTable();
        $uidField = $authCode->getReferenceTableUidField();
        $uid = (int)$authCode->getReferenceTableUid();

        $res = $this->typo3Db->exec_SELECTquery('*', $table, $uidField . '=' . $uid);
        if ($res && $this->typo3Db->sql_num_rows($res)) {
            $authCodeRecord = $this->typo3Db->sql_fetch_assoc($res);
        }

        $this->typo3Db->sql_free_result($res);

        return $authCodeRecord;
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
        $table = $authCode->getReferenceTable();
        $uidField = $authCode->getReferenceTableUidField();
        $uid = $authCode->getReferenceTableUid();

        if (!$forceDeletion
            && isset($GLOBALS['TCA'][$table]['ctrl']['delete'])
            && trim($GLOBALS['TCA'][$table]['ctrl']['delete']) !== ''
        ) {
            $deleteColumn = $GLOBALS['TCA'][$table]['ctrl']['delete'];
            $fieldValues[$deleteColumn] = 1;
            $this->typo3Db->exec_UPDATEquery($table, $uidField . '=' . (int)$uid, $fieldValues);
        } else {
            $this->typo3Db->exec_DELETEquery($table, $uidField . '=' . (int)$uid);
        }
    }
}
