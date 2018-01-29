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
		'a21041' => 'Pi 2 Model B (Embest, CN)',
		'a22042' => 'Pi 2 Model B with BCM2837 (Embest, CN)',
		'a02082' => 'Pi 3 Model B (Sony, UK)',
		'a22082' => 'Pi 3 Model B (Embest, CN)',
		'a32082' => 'Pi 3 Model B (Sony, JP)',
	];

	protected $revision;

    /**
     * @throws ModelException
     */
	public function __construct()
	{
		if(false === ($this->revision = $this->evaluateRevision())) {
            throw new ModelException('This seems not to be a Raspberry Pi model');
        }
	}

	protected function evaluateRevision()
	{
		if(false === ($content = file_get_contents('/proc/cpuinfo'))) {
            return false;
        }

		if(0 === preg_match('/Revision\s+:\s+([a-f0-9]+)/', $content, $match)) {
            return false;
        }

		return $match[1];
	}

	/**
	 * Get the valid GPIO pin map
	 *
	 * @return array
	 */
	public function getGPIOPins()
	{
		if(true === in_array($this->revision, ['a01041', 'a21041', 'a22042'], true)) {
			// Pi 2 revs
			return range(2, 27);
		}

		if(true === in_array($this->revision, ['a02082', 'a22082', 'a32082'], true)) {
			// Pi 3 revs
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
		if(false === isset(self::$modelNameMap[$this->revision])) {
            return 'unknown';
        }

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
