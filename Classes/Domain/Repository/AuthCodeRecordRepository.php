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

use Exception;
use PDO;
use Tx\Authcode\Domain\Model\AuthCode;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class providing helper functions for database records associated to auth codes.
 */
class AuthCodeRecordRepository implements SingletonInterface
{
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
        $queryBuilder = $this->getQueryBuilder($updateTable);
        $queryBuilder->update($updateTable);

        $uidField = $authCode->getReferenceTableUidField();
        $uid = $authCode->getReferenceTableUid();
        $this->addUidContstraint($queryBuilder, $uidField, $uid);

        $hiddenField = $authCode->getReferenceTableHiddenField();
        $queryBuilder->set($hiddenField, $authCode->getReferenceTableHiddenFieldMustBeTrue() ? 1 : 0);

        if ($updateTimestamp
            && !empty($GLOBALS['TCA'][$updateTable]['ctrl']['tstamp'])
        ) {
            $tstampField = $GLOBALS['TCA'][$updateTable]['ctrl']['tstamp'];
            $queryBuilder->set($tstampField, $GLOBALS['EXEC_TIME']);
        }

        $queryBuilder->execute();
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
        $queryBuilder = $this->getQueryBuilder($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->select('*');
        $queryBuilder->from($table);

        $uidField = $authCode->getReferenceTableUidField();
        $uid = $authCode->getReferenceTableUid();
        $this->addUidContstraint($queryBuilder, $uidField, $uid);

        $result = $queryBuilder->execute();
        $authCodeRecord = $result->fetch(PDO::FETCH_ASSOC);

        return $authCodeRecord ?: null;
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
        $queryBuilder = $this->getQueryBuilder($table);

        $uidField = $authCode->getReferenceTableUidField();
        $uid = $authCode->getReferenceTableUid();

        if (!$forceDeletion
            && isset($GLOBALS['TCA'][$table]['ctrl']['delete'])
            && trim($GLOBALS['TCA'][$table]['ctrl']['delete']) !== ''
        ) {
            $deleteColumn = $GLOBALS['TCA'][$table]['ctrl']['delete'];
            $queryBuilder->update($table);
            $this->addUidContstraint($queryBuilder, $uidField, $uid);
            $queryBuilder->set($deleteColumn, 1);
            $queryBuilder->execute();
            return;
        }

        $queryBuilder->delete($table);
        $this->addUidContstraint($queryBuilder, $uidField, $uid);
        $queryBuilder->execute();
    }

    private function addUidContstraint(QueryBuilder $queryBuilder, $uidField, $uid)
    {
        $queryBuilder->where(
            $queryBuilder->expr()->eq(
                $uidField,
                $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
            )
        );
    }

    private function getConnectionPool()
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }

    private function getQueryBuilder($table)
    {
        return $this->getConnectionPool()->getQueryBuilderForTable($table);
    }
}
