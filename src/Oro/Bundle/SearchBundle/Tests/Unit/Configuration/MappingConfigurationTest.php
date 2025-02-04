<?php

namespace Oro\Bundle\SearchBundle\Tests\Unit\Configuration;

use Oro\Bundle\SearchBundle\Configuration\MappingConfiguration;
use Oro\Component\Testing\Unit\LoadTestCaseDataTrait;
use Symfony\Component\Config\Definition\Processor;

class MappingConfigurationTest extends \PHPUnit\Framework\TestCase
{
    use LoadTestCaseDataTrait;

    /**
     * @dataProvider processConfigurationDataProvider
     */
    public function testProcessConfiguration(array $configs, array $expected)
    {
        $processor = new Processor();

        $this->assertEquals(
            $expected,
            $processor->processConfiguration(new MappingConfiguration(), $configs)
        );
    }

    public function processConfigurationDataProvider(): iterable
    {
        return $this->getTestCaseData(__DIR__ . DIRECTORY_SEPARATOR . 'mapping_configuration');
    }
}
