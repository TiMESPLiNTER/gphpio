<?php

namespace timesplinter\gphpio;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015 by TiMESPLiNTER Webdevelopment
 */
class GPIO
{
	const MODE_INPUT = 'in';
	const MODE_OUTPUT = 'out';

	const SYSFS_BASE_PATH = '/sys/class/gpio/';
	const SYSFS_PIN_PATH = 'gpio%s/';
	const SYSFS_EXPORT_PATH = 'export';
	const SYSFS_UNEXPORT_PATH = 'unexport';

	/**
     * @var Model
     */
	protected $model;

    /**
     * @param Model $model
     */
	public function __construct(Model $model)
	{
		$this->model = $model;
	}

    /**
     * @param int $pin
     * @param string|null $mode
     * @return bool
     * @throws GPIOException
     */
	public function export($pin, $mode = null)
	{
		if(false === $this->isValid($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');
        }

		if(true === $this->isExported($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is already exported');
        }

		if(false === file_put_contents(self::SYSFS_BASE_PATH . self::SYSFS_EXPORT_PATH, $pin)) {
            return false;
        }

		if(null === $mode) {
            return true;
        }

		$waited = 0;
		$directionFile = sprintf(self::SYSFS_BASE_PATH . self::SYSFS_PIN_PATH, $pin) . 'direction';

		while(false === ($fp = @fopen($directionFile, 'w'))) {
			if($waited > 1000) {
                throw new GPIOException('Can not set direction for pin #' . $pin);
            }

			++$waited;
			usleep(10);
		}

		fclose($fp);

		return $this->mode($pin, $mode);
	}

    /**
     * @param int $pin
     * @return bool
     * @throws GPIOException
     */
	public function unexport($pin)
	{
		if(false === $this->isValid($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');
        }

		if(false === $this->isExported($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not exported');
        }

		if(false === file_put_contents(self::SYSFS_BASE_PATH . self::SYSFS_UNEXPORT_PATH, $pin)) {
            return false;
        }

		return false === file_exists(sprintf(self::SYSFS_BASE_PATH . self::SYSFS_PIN_PATH, $pin));
	}

    /**
     * @param int $pin
     * @return bool
     * @throws GPIOException
     */
	public function isExported($pin)
	{
		if(false === $this->isValid($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');
        }

		return false !== file_exists(sprintf(self::SYSFS_BASE_PATH . self::SYSFS_PIN_PATH, $pin));
	}

    /**
     * @param $pin
     * @return bool
     */
	public function isValid($pin)
	{
		return in_array($pin, $this->model->getGPIOPins(), true);
	}

    /**
     * @param int $pin
     * @param string $mode Either one of "in" or "out"
     * @return bool
     * @throws GPIOException
     */
	public function mode($pin, $mode)
	{
		if(false === $this->isValid($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');
        }

		return file_put_contents(sprintf(self::SYSFS_BASE_PATH . self::SYSFS_PIN_PATH, $pin) . 'direction', $mode) !== false;
	}

    /**
     * @param int $pin
     * @return string
     * @throws GPIOException
     */
	public function read($pin)
	{
		if(false === $this->isValid($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');
        }

		if(false === $this->isExported($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not exported');
        }

		return trim(file_get_contents(sprintf(self::SYSFS_BASE_PATH . self::SYSFS_PIN_PATH, $pin) . 'value'));
	}

    /**
     * @param int $pin
     * @param int $value
     * @return bool
     * @throws GPIOException
     */
	public function write($pin, $value)
	{
		if(false === $this->isValid($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');
        }

		if(false === $this->isExported($pin)) {
            throw new GPIOException('Pin #' . $pin . ' is not exported');
        }

		return false !== file_put_contents(sprintf(self::SYSFS_BASE_PATH . self::SYSFS_PIN_PATH, $pin) . 'value', $value);
	}
}