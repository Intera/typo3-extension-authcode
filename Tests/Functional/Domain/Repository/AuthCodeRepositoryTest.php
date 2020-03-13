<?php

namespace Tx\Authcode\Tests\Functional\Domain\Repository;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Tx\Authcode\Domain\Model\AuthCode;
use Tx\Authcode\Domain\Repository\AuthCodeRepository;
use Tx\Authcode\Tests\Functional\AbstractFunctionalTest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AuthCodeRepositoryTest extends AbstractFunctionalTest
{
    /**
     * @var AuthCodeRepository
     */
    private $authCodeRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->authCodeRepository = $this->getObjectManager()->get(AuthCodeRepository::class);
    }

    public function testGenerateAuthCodeGeneratesRandomMd5()
    {
        $firstCode = new AuthCode();
        $this->authCodeRepository->generateIndependentAuthCode($firstCode, 'testid', 'test');

        $secondCode = new AuthCode();
        $this->authCodeRepository->generateIndependentAuthCode($secondCode, 'testid2', 'test');

        $this->assertNotEquals($firstCode->getAuthCode(), $secondCode->getAuthCode());
        $this->assertEquals(32, strlen($firstCode->getAuthCode()));
    }
}
