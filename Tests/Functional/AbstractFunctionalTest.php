<?php

namespace Tx\Authcode\Tests\Functional;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractFunctionalTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = ['extbase'];

    protected $testExtensionsToLoad = ['typo3conf/ext/authcode'];

    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
