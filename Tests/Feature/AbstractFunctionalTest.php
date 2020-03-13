<?php

namespace Tx\Authcode\Tests\Feature;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

abstract class AbstractFunctionalTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = ['extbase'];

    protected $testExtensionsToLoad = ['typo3conf/ext/authcode'];

    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}
