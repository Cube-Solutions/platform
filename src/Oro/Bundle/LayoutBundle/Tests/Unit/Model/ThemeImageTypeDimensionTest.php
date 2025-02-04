<?php

namespace Oro\Bundle\LayoutBundle\Tests\Unit\Model;

use Oro\Bundle\LayoutBundle\Model\ThemeImageTypeDimension;

class ThemeImageTypeDimensionTest extends \PHPUnit\Framework\TestCase
{
    private const NAME = 'dim';
    private const WIDTH = 100;
    private const HEIGHT = 200;
    private const OPTION_1 = 'option1';
    private const OPTION_2 = 'option2';

    /** @var ThemeImageTypeDimension */
    private $imageTypeDimension;

    protected function setUp(): void
    {
        $this->imageTypeDimension = new ThemeImageTypeDimension(self::NAME, self::WIDTH, self::HEIGHT);
    }

    public function testAccessors()
    {
        $this->assertEquals(self::NAME, $this->imageTypeDimension->getName());
        $this->assertEquals(self::WIDTH, $this->imageTypeDimension->getWidth());
        $this->assertEquals(self::HEIGHT, $this->imageTypeDimension->getHeight());
    }

    public function testHasOption()
    {
        $this->imageTypeDimension = new ThemeImageTypeDimension(self::NAME, self::WIDTH, self::HEIGHT, [
            self::OPTION_1 => 1
        ]);

        $this->assertTrue($this->imageTypeDimension->hasOption(self::OPTION_1));
        $this->assertFalse($this->imageTypeDimension->hasOption(self::OPTION_2));
    }

    public function testGetOption()
    {
        $optionValue = 1;
        $this->imageTypeDimension = new ThemeImageTypeDimension(self::NAME, self::WIDTH, self::HEIGHT, [
            self::OPTION_1 => $optionValue
        ]);

        $this->assertEquals($optionValue, $this->imageTypeDimension->getOption(self::OPTION_1));
        $this->assertNull($this->imageTypeDimension->getOption(self::OPTION_2));
    }
}
