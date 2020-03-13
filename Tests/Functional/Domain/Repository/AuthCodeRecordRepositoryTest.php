<?php

namespace Tx\Authcode\Tests\Functional\Domain\Repository;

use Doctrine\DBAL\FetchMode;
use Tx\Authcode\Domain\Model\AuthCode;
use Tx\Authcode\Domain\Repository\AuthCodeRecordRepository;
use Tx\Authcode\Tests\Functional\AbstractFunctionalTest;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AuthCodeRecordRepositoryTest extends AbstractFunctionalTest
{
    /**
     * @var AuthCodeRecordRepository
     */
    private $recordRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->recordRepository = $this->getObjectManager()->get(AuthCodeRecordRepository::class);
    }

    public function testEnableAssociatedRecordEnablesRecord()
    {
        $this->importContentDataset();

        $contentData = $this->fetchContentData();
        $this->assertEquals(1, $contentData['hidden']);

        $this->callEnableRecord(false);

        $contentData = $this->fetchContentData();
        $this->assertEquals(0, $contentData['hidden']);
    }

    public function testEnableAssociatedRecordKeepsTimestamp()
    {
        $this->importContentDataset();

        $contentData = $this->fetchContentData();
        $this->assertEquals(1366642540, $contentData['tstamp']);

        $this->callEnableRecord(false);

        $contentData = $this->fetchContentData();
        $this->assertEquals(1366642540, $contentData['tstamp']);
    }

    public function testEnableAssociatedRecordUpdatesTimestamp()
    {
        $this->importContentDataset();

        $contentData = $this->fetchContentData();
        $this->assertEquals(1366642540, $contentData['tstamp']);

        $this->callEnableRecord(true);

        $contentData = $this->fetchContentData();
        $this->assertGreaterThan(1366642540, $contentData['tstamp']);
    }

    public function testGetAuthCodeRecordFromDB()
    {
        $this->importContentDataset();
        $contentAuthCode = $this->buildContentAuthCode();
        $data = $this->recordRepository->getAuthCodeRecordFromDB($contentAuthCode);
        $this->assertEquals(1, $data['uid']);
        $this->assertEquals('Hidden record', $data['header']);
    }

    public function testRemoveAssociatedRecordDeletesRecord()
    {
        $this->importContentDataset();
        $contentData = $this->fetchContentData();
        $this->assertEquals(1, $contentData['uid']);

        $contentAuthCode = $this->buildContentAuthCode();
        $this->recordRepository->removeAssociatedRecord($contentAuthCode, true);

        $contentData = $this->fetchContentData();
        $this->assertEmpty($contentData);
    }

    public function testRemoveAssociatedRecordMarksRecordAsDeleted()
    {
        $this->importContentDataset();
        $contentData = $this->fetchContentData();
        $this->assertEquals(1, $contentData['uid']);

        $contentAuthCode = $this->buildContentAuthCode();
        $this->recordRepository->removeAssociatedRecord($contentAuthCode);

        $contentData = $this->fetchContentData();
        $this->assertEquals(1, $contentData['deleted']);
    }

    private function buildContentAuthCode()
    {
        $authCode = new AuthCode();
        $authCode->setReferenceTable('tt_content');
        $authCode->setReferenceTableUid(1);
        $authCode->setReferenceTableHiddenField('hidden');
        return $authCode;
    }

    private function callEnableRecord($updateTimestamp)
    {
        $authCode = $this->buildContentAuthCode();
        $this->recordRepository->enableAssociatedRecord($authCode, $updateTimestamp);
    }

    /**
     * @return array|false
     */
    private function fetchContentData()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->select('*')->from('tt_content')->where('uid=1');
        return $queryBuilder->execute()->fetch(FetchMode::ASSOCIATIVE);
    }

    private function importContentDataset()
    {
        $this->importDataSet(__DIR__ . '/../../Fixtures/Database/tt_content.xml');
    }
}
