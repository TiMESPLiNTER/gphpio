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

	const SYSFS_PATH = '/sys/class/gpio/gpio%s/';

	/** @var Model */
	protected $model;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function export($pin, $mode = null)
	{
		if($this->isValid($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');

		if($this->isExported($pin) === true)
			throw new GPIOException('Pin #' . $pin . ' is already exported');

		if(file_put_contents(self::SYSFS_PATH . 'export', $pin) === false)
			return false;

		if($mode === null)
			return true;

		return $this->mode($pin, $mode);
	}

	public function unexport($pin)
	{
		if($this->isValid($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');

		if($this->isExported($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not exported');

		if(file_put_contents(self::SYSFS_PATH . 'unexport', $pin) === false)
			return false;

		return file_exists(sprintf(self::SYSFS_PATH, $pin)) === false;
	}

	public function isExported($pin)
	{
		if($this->isValid($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');

		return file_exists(sprintf(self::SYSFS_PATH, $pin)) !== false;
	}

	public function isValid($pin)
	{
		return in_array($pin, $this->model->getGPIOPins());
	}

	public function mode($pin, $mode)
	{
		if($this->isValid($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');

		return file_put_contents(sprintf(self::SYSFS_PATH, $pin) . 'direction', $mode) !== false;
	}

	public function read($pin)
	{
		if($this->isValid($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');

		if($this->isExported($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not exported');

		return trim(file_get_contents(sprintf(self::SYSFS_PATH, $pin) . 'value'));
	}

	public function write($pin, $value)
	{
		if($this->isValid($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not a valid GPIO pin for this model');

		if($this->isExported($pin) === false)
			throw new GPIOException('Pin #' . $pin . ' is not exported');

		return file_put_contents(sprintf(self::SYSFS_PATH, $pin) . 'value', $value) !== false;
	}
}