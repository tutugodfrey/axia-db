<?php
App::uses('TimeHelper', 'View/Helper');

/**
 * Custom TimeHelper for the App
 *
 */
class AxiaTimeHelper extends TimeHelper {

/**
 * Default formats for the app
 */
	const FORMAT_DATE = 'M j, Y';
	const FORMAT_TIME = 'g:i a';
	const FORMAT_DATETIME = 'M j, Y g:i a';

/**
 * Days of the week
 */
	private static $__days = array(
		'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
	);

/**
 * relativeTime()
 * Returns a relative time string based on the time parameter. Depending on the time parameter passed this function will
 * Return "today" followed by the time
 * Return "yesterday" followed by the time
 * Wil return "Last <day of the week> at HH:mm:ss"
 * Or will return a formatted for time older than 7 days.
 *
 * @param int $time a simple UNIX timestamp
 * @param string $format  a fallback format, for when the date falls is beyond out of the 'Last' date range
 * @return string 
 */
	public static function relativeTime($time, $format = self::FORMAT_DATETIME) {
		if (date('d/m/Y', $time) === date('d/m/Y')) {
			return 'today' . ' at ' . date(self::FORMAT_TIME, $time);
		} elseif (date('d/m/Y', $time) === date('d/m/Y', time() - 3600 * 24)) {
			return 'Yesterday ' . date(self::FORMAT_TIME, $time);
		} elseif ((time() - $time) < 3600 * 24 * 8) {
			return 'last ' . self::$__days[date('w', $time)] . ' ' . date(self::FORMAT_TIME, $time);
		} else {
			return date($format, $time);
		}
	}
/**
 * Display a date with the app default format
 *
 * @param string $date Date to display
 * @return string
 */
	public function date($date) {
		return $this->format(self::FORMAT_DATE, $date);
	}

/**
 * Change current date format to a new format
 *
 * @param string $date Date to display
 * @param string $curFormat Current format of date string
 * @param string $newFormat New format for date
 * @return string
 */
	public function dateChangeFormat($date, $curFormat, $newFormat) {
		return date_format(date_create_from_format($curFormat, $date), $newFormat);
	}

/**
 * Display a time with the app default format
 *
 * @param string $time Time to display
 * @return string
 */
	public function time($time) {
		return $this->format(self::FORMAT_TIME, $time);
	}

/**
 * Display a datetime with the app default format
 *
 * @param string $datetime Datetime to display
 * @return string
 */
	public function datetime($datetime) {
		return $this->format(self::FORMAT_DATETIME, $datetime);
	}
}
