<?php

require_once 'config.php';
require_once '../bpost.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * test case.
 */
class bPostTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var bPost
	 */
	private $bpost;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->bpost = new bPost(ACCOUNT_ID, PASSPHRASE);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->bpost = null;
		parent::tearDown();
	}

	/**
	 * Tests bPost->getTimeOut()
	 */
	public function testGetTimeOut()
	{
		$this->bpost->setTimeOut(5);
		$this->assertEquals(5, $this->bpost->getTimeOut());
	}

	/**
	 * Tests bPost->getUserAgent()
	 */
	public function testGetUserAgent()
	{
		$this->bpost->setUserAgent('testing/1.0.0');
		$this->assertEquals('PHP bPost/' . bPost::VERSION . ' testing/1.0.0', $this->bpost->getUserAgent());
	}

	/**
	 * Tests bpost->createNationalLabel
	 */
	public function testCreateNationalLabel()
	{
		$var = $this->bpost->createNationalLabel(660, 1, null, true);

		$this->assertArrayHasKey('orderReference', $var);
		$this->assertArrayHasKey('barcode', $var);
		$this->assertArrayHasKey('pdf', $var);
	}
}

