<?php

namespace timesplinter\gphpio\tests;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use timesplinter\gphpio\GPIO;
use timesplinter\gphpio\GPIOException;
use timesplinter\gphpio\Model;

final class GPIOTest extends TestCase
{

    use PHPMock;

    public function testExportThrowsExceptionOnInvalidPin()
    {
        $invalidPin = 2;

        self::expectException(GPIOException::class);
        self::expectExceptionMessage(sprintf('Pin #%d is not a valid GPIO pin for this model', $invalidPin));

        $model = $this->getModel();
        $model
            ->expects(self::once())
            ->method('getGPIOPins')
            ->willReturn([1]);

        $gpio = new GPIO($model);

        $gpio->export($invalidPin);
    }

    public function testExportThrowsExceptionIfPinIsAlreadyExported()
    {
        $pin = 1;

        self::expectException(GPIOException::class);
        self::expectExceptionMessage(sprintf('Pin #%d is already exported', $pin));

        $model = $this->getModel();
        $model
            ->expects(self::atLeastOnce())
            ->method('getGPIOPins')
            ->willReturn([1]);

        $fileExists = $this->getFunctionMock(self::getClassNamespace(), 'file_exists');
        $fileExists
            ->expects(self::once())
            ->with(sprintf('/sys/class/gpio/gpio%s/', $pin))
            ->willReturn(true);

        $gpio = new GPIO($model);

        $gpio->export($pin);
    }

    /**
     * @return Model|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getModel()
    {

        return $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGPIOPins', 'getName'])
            ->getMockForAbstractClass();
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    private static function getClassNamespace()
    {
        return (new \ReflectionClass(GPIO::class))->getNamespaceName();
    }
}
