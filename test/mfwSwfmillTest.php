<?php
require_once __DIR__.'/initialize.php';

/**
 * Test class for mfwSwfmill.
 * Generated by PHPUnit on 2013-01-14 at 16:32:37.
 */
class mfwSwfmillTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testSwfmillVersion()
	{
		mfwServerEnv::setEnv('unittest');
		$h = popen(mfwServerEnv::swfmill().' 2>&1','r');
		$r = fread($h,50);
		pclose($h);
		$this->assertStringStartsWith("\nswfmill 0.3.1",$r);
	}

	/**
	 * @dataProvider swf2xmlProvider
	 * @depends testSwfmillVersion
	 */
	public function testSwf2xml($swffile,$xmlfile,$cp932)
	{
		mfwServerEnv::setEnv('unittest');

		$swf = file_get_contents($swffile);
		$xml = file_get_contents($xmlfile);

		$swf2xml = mfwSwfmill::swf2xml($swf,$cp932);
		$this->assertEquals($xml,$swf2xml);
	}

	public function swf2xmlProvider()
	{
		$dir = __DIR__.'/misc';
		return array(
			array(
				"$dir/flashlite1_cp932.swf",
				"$dir/flashlite1_cp932.swf.xml",
				true,
				),
			);
	}

	/**
	 * @dataProvider xml2swfProvider
	 * @depends testSwfmillVersion
	 */
	public function testXml2swf($xmlfile,$swffile,$cp932)
	{
		mfwServerEnv::setEnv('unittest');

		$xml = file_get_contents($xmlfile);
		$swf = file_get_contents($swffile);

		$xml2swf = mfwSwfmill::xml2swf($xml,$cp932);
		//$this->assertEquals($swf,$xml2swf,"differ from $swffile");
		$this->assertTrue($swf===$xml2swf);
	}

	public function xml2swfProvider()
	{
		$dir = __DIR__.'/misc';
		return array(
			array(
				"$dir/flashlite1_cp932.swf.xml",
				"$dir/flashlite1_cp932.swf.xml.swf",
				true,
				),
			);
	}

	public function testNoSetting()
	{
		elb_start();
		mfwServerEnv::setEnv('noserver');

		try{
			$out = mfwSwfmill::swf2xml('dummy');
			$this->fail('no exception');
		}
		catch(RuntimeException $e){
			$msg = $e->getMessage();
			$this->assertStringStartsWith('no swfmill command',$msg);
		}

		try{
			$out = mfwSwfmill::xml2swf('dummy');
			$this->fail('no exception');
		}
		catch(RuntimeException $e){
			$msg = $e->getMessage();
			$this->assertStringStartsWith('no swfmill command',$msg);
		}
	}

	public function testIllegalCommand()
	{
		mfwServerEnv::setEnv('illegalenv');

		elb_start();
		$out = mfwSwfmill::swf2xml('dummy');
		$err = elb_get_clean();

		$this->assertRegexp('/notfound/',$err);
		$this->assertEquals('',$out);

		elb_start();
		$out = mfwSwfmill::xml2swf('dummy');
		$err = elb_get_clean();

		$this->assertRegexp('/notfound/',$err);
		$this->assertEquals('',$out);

		echo elb_get_clean();
	}

}
?>
