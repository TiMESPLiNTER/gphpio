<?php

namespace timesplinter\gphpio;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015 by TiMESPLiNTER Webdevelopment
 */
class RPi extends Model
{
	protected static $modelNameMap = [
		'0002' => 'Model B Revision 1.0',
		'0003' => 'Model B Revision 1.0 + ECN0001',
		'0004' => 'Model B Revision 2.0',
		'0005' => 'Model B Revision 2.0',
		'0006' => 'Model B Revision 2.0',
		'0007' => 'Model A',
		'0008' => 'Model A',
		'0009' => 'Model A',
		'000d' => 'Model B Revision 2.0',
		'000e' => 'Model B Revision 2.0',
		'000f' => 'Model B Revision 2.0',
		'0010' => 'Model B+',
		'0011' => 'Compute Module',
		'0012' => 'Model A+',
		'a01041' => 'Pi 2 Model B (Sony, UK)',
		'a21041' => 'Pi 2 Model B (Embest, CN)'
	];

	protected $revision;

	public function __construct()
	{
		if(($this->revision = $this->evaluateRevision()) === false)
			throw new ModelException('This seems not to be a Raspberry Pi model');
	}

	protected function evaluateRevision()
	{
		if(($content = file_get_contents('/proc/cpuinfo')) === false)
			return false;

		if(preg_match('/Revision\s+:\s+([a-f0-9]+)/', $content, $match) === 0)
			return false;

		return $match[1];
	}

	/**
	 * Get the valid GPIO pin map
	 *
	 * @return array
	 */
	public function getGPIOPins()
	{
		if(in_array($this->revision, ['a01041', 'a21041']) === true) {
			// Pi 2 revs
			return range(2, 27);
		}

		// Pi 1 revs
		return [0, 1, 4, 7, 8, 9, 10, 11, 14, 15, 17, 18, 21, 22, 23, 25];
	}

	/**
	 * Get the revision name
	 *
	 * @return string
	 */
	public function getName()
	{
		if(isset(self::$modelNameMap[$this->revision]) === false)
			return 'unknown';

		return self::$modelNameMap[$this->revision];
	}

	/**
	 * @return string
	 */
	public function getRevision()
	{
		return $this->revision;
	}
}