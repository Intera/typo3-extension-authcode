<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

defined('TYPO3_MODE') || die();

/** @var $extbaseObjectContainer \TYPO3\CMS\Extbase\Object\Container\Container */
$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Extbase\Object\Container\Container::class
);

if (class_exists('TYPO3\\CMS\\Core\\Database\\ConnectionPool')) {
    $extbaseObjectContainer->registerImplementation(
        \Tx\Authcode\Domain\Repository\AuthCodeRecord\AuthCodeRecordAdapterInterface::class,
        \Tx\Authcode\Domain\Repository\AuthCodeRecord\DbalDatabaseAdapter::class
    );
} else {
    $extbaseObjectContainer->registerImplementation(
        \Tx\Authcode\Domain\Repository\AuthCodeRecord\AuthCodeRecordAdapterInterface::class,
        \Tx\Authcode\Domain\Repository\AuthCodeRecord\TYPO3DatabaseAdapter::class
    );
}

unset($extbaseObjectContainer);
