<?php
namespace ZBateson\MailMimeParser\Stream;

use PHPUnit_Framework_TestCase;
use ZBateson\MailMimeParser\SimpleDi;

/**
 * Description of PartStreamTest
 *
 * @group PartStream
 * @covers ZBateson\MailMimeParser\Stream\PartStream
 * @author Zaahid Bateson
 */
class PartStreamTest extends PHPUnit_Framework_TestCase
{
    private $di;
    private $registry;
    
    protected function setUp()
    {
        $this->di = SimpleDi::singleton();
        $this->registry = $this->di->getPartStreamRegistry();
    }
    
    public function testRegisteringAndUnregistering()
    {
        $mem = fopen('php://memory', 'rw');
        fwrite($mem, 'This is a test');
        $mem2 = fopen('php://memory', 'rw');
        fwrite($mem2, 'This is a test');
        $mem3 = fopen('php://memory', 'rw');
        fwrite($mem3, 'This is a test');
        
        $this->registry->register(1, $mem);
        $this->registry->register(2, $mem2);
        $this->registry->register(3, $mem3);
        
        $ps = @fopen('mmp-mime-message://1?start=1&end=4', 'r');
        $ps2 = @fopen('mmp-mime-message://2?start=1&end=4', 'r');
        $ps3 = @fopen('mmp-mime-message://3?start=1&end=4', 'r');
        
        $this->assertNotNull($ps);
        $this->assertNotNull($ps2);
        $this->assertNotNull($ps3);
        
        fclose($ps);
        fclose($ps2);
        fclose($ps3);
        
        $ps2 = @fopen('mmp-mime-message://2?start=1&end=4', 'r');
        $this->assertFalse($ps2);
        
        $ps = @fopen('mmp-mime-message://1?start=1&end=4', 'r');
        $this->assertFalse($ps);
        
        $ps3 = @fopen('mmp-mime-message://3?start=1&end=4', 'r');
        $this->assertFalse($ps3);
    }
    
    public function testReadLimits()
    {
        $mem = fopen('php://memory', 'rw');
        fwrite($mem, 'This is a test');
        $this->registry->register('testReadLimits', $mem);
        
        $res = fopen('mmp-mime-message://testReadLimits?start=1&end=4', 'r');
        $this->assertNotNull($res);
        $str = stream_get_contents($res);
        $this->assertEquals('his', $str);
        
        fclose($res);
    }
    
    public function testReadLimitsToEnd()
    {
        $mem = fopen('php://memory', 'rw');
        fwrite($mem, 'test');
        $this->registry->register('testReadLimitsToEnd', $mem);
        
        $res = fopen('mmp-mime-message://testReadLimitsToEnd?start=0&end=4', 'r');
        $this->assertNotNull($res);
        $str = stream_get_contents($res);
        $this->assertEquals('test', $str);
        
        fclose($res);
    }
    
    public function testPosition()
    {
        $mem = fopen('php://memory', 'rw');
        fwrite($mem, 'This is a test');
        $this->registry->register('testReadLimits', $mem);
        
        $res = fopen('mmp-mime-message://testReadLimits?start=1&end=4', 'r');
        $this->assertNotNull($res);
        $this->assertEquals(0, ftell($res));
        $str = stream_get_contents($res);
        $this->assertEquals(3, ftell($res));
        
        fclose($res);
    }
    
    public function testEof()
    {
        $mem = fopen('php://memory', 'rw');
        fwrite($mem, 'This is a test');
        $this->registry->register('testReadLimits', $mem);
        
        $res = fopen('mmp-mime-message://testReadLimits?start=1&end=4', 'r');
        $this->assertNotNull($res);
        $this->assertFalse(feof($res));
        $str = stream_get_contents($res);
        $this->assertTrue(feof($res));
        
        fclose($res);
    }
    
    public function testSeek()
    {
        $mem = fopen('php://memory', 'rw');
        fwrite($mem, 'This is a test');
        $this->registry->register('testReadLimits', $mem);
        
        $res = fopen('mmp-mime-message://testReadLimits?start=1&end=4', 'r');
        $this->assertNotNull($res);
        
        $this->assertEquals(-1, fseek($res, -1, SEEK_SET));
        $this->assertEquals(-1, fseek($res, 4, SEEK_SET));
        $this->assertEquals(-1, fseek($res, 1, SEEK_END));
        $this->assertEquals(-1, fseek($res, -1, SEEK_CUR));
        
        $this->assertEquals(0, fseek($res, 2, SEEK_SET));
        $str = stream_get_contents($res);
        $this->assertEquals('s', $str);
        
        $this->assertEquals(0, fseek($res, -2, SEEK_CUR));
        $str = stream_get_contents($res);
        $this->assertEquals('is', $str);
        
        $this->assertEquals(0, fseek($res, -1, SEEK_END));
        $str = stream_get_contents($res);
        $this->assertEquals('s', $str);
        
        fclose($res);
    }
}
