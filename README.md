# php-gpio

This library provides a nice OO interface to interact with the GPIO pins of for example RaspberryPi (2).

## Setup

For RaspberryPi (2) please make sure that the files at `/sys/class/gpio` are owned by `root:gpio` and that the user which
executes the PHP script using this library is also in the group `gpio`. This should be the case anyway as long as you have
already run the `raspi-config` tool on installation.

Else you may need to apply the following changes [described here](http://www.element14.com/community/message/139528/l/re-piface-digital-2--setup-and-use#139528).

## Example

The "Hello world" LED-blink script would look like that:

```php
$gpio = new GPIO();
$pin = 17;

$gpio->bindPin($pin, GPIO::MODE_OUTPUT);

while(true) {
	$gpio->writePin($pin, 1);
	echo 'The pin is now: ' , $gpio->readPin($pin) , PHP_EOL;
	sleep(1);

	$gpio->writePin($pin, 0);
	echo 'The pin is now: ' , $gpio->readPin($pin) , PHP_EOL;
	sleep(1);
}
```