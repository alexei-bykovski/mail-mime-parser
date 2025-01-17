<?php
namespace ZBateson\MailMimeParser\Message\Factory;

use LegacyPHPUnit\TestCase;

/**
 * PartHeaderContainerFactoryTest
 *
 * @group PartHeaderContainerFactory
 * @group MessagePart
 * @covers ZBateson\MailMimeParser\Message\Factory\PartHeaderContainerFactory
 * @author Zaahid Bateson
 */
class PartHeaderContainerFactoryTest extends TestCase
{
    private $instance;

    protected function legacySetUp()
    {
        $mockhf = $this->getMockBuilder('ZBateson\MailMimeParser\Header\HeaderFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->instance = new PartHeaderContainerFactory(
            $mockhf
        );
    }

    public function testNewInstance()
    {
        $container = $this->instance->newInstance();
        $this->assertInstanceOf(
            '\ZBateson\MailMimeParser\Message\PartHeaderContainer',
            $container
        );
    }
}
