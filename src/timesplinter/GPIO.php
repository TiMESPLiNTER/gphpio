<?php

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015 by TiMESPLiNTER Webdevelopment
 */
class GPIO
{
	const MODE_INPUT = 'in';
	const MODE_OUTPUT = 'out';

	const SYSFS_PATH = '/sys/class/gpio/gpio%c/';

	public function bindPin($pin, $mode = null)
	{
		if(file_exists(sprintf(self::SYSFS_PATH, $pin)) === true)
			return false;

		if(file_put_contents(self::SYSFS_PATH . 'export', $pin) === false)
			return false;

		if($mode === null)
			return true;

		return $this->setMode($pin, $mode);
	}

	public function unbindPin($pin)
	{
		if(file_exists(sprintf(self::SYSFS_PATH, $pin)) === false)
			return false;

		if(file_put_contents(self::SYSFS_PATH . 'unexport', $pin) === false)
			return false;

		return file_exists(sprintf(self::SYSFS_PATH, $pin)) === false;
	}

	public function setPinMode($pin, $mode)
	{
		return file_put_contents(sprintf(self::SYSFS_PATH, $pin) . 'direction', $mode) !== false;
	}

	public function readPin($pin)
	{
		return trim(file_get_contents(sprintf(self::SYSFS_PATH, $pin) . 'value'));
	}

	public function writePin($pin, $value)
	{
		return file_put_contents(sprintf(self::SYSFS_PATH, $pin) . 'value', $value) !== false;
	}
}