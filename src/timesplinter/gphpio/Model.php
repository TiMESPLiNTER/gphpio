<?php


namespace timesplinter\gphpio;


/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015 by TiMESPLiNTER Webdevelopment
 */
abstract class Model
{
	/**
	 * @return array
	 */
	abstract public function getGPIOPins();

	/**
	 * @return string
	 */
	abstract public function getName();
}