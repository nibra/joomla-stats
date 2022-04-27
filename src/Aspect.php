<?php
namespace Joomla\Statistics;

class Aspect
{
	/**
	 * Monthly reports
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - year  - the year of the recording
	 * - month - the month of the recording
	 * - count - the absolute number of reports in that month.
	 */
	public const REPORTS = "REPORTS";

	/**
	 * Monthly stats for CMS versions
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - cms_short  - the short version of the CMS, e.g. major.minor
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified version in that month.
	 * - percentage    - the relative frequency of mentions of the specified version in that month.
	 */
	public const CMS_VERSION = "CMS_VERSION";

	/**
	 * Monthly stats for PHP versions
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - php_short  - the short version of PHP, e.g. major.minor
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified version in that month.
	 * - percentage - the relative frequency of mentions of the specified version in that month.
	 */
	public const PHP_VERSION = "PHP_VERSION";

	/**
	 * Monthly stats for database versions
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - db_type    - the name of the database, e.g. MySQL or MariaDB
	 * - db_short   - the short version of PHP, e.g. major.minor
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified database and version in that month.
	 * - percentage - the relative frequency of mentions of the specified database and version in that month.
	 */
	public const DB_VERSION = "DB_VERSION";

	/**
	 * Monthly stats for databases
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - db_type    - the name of the database, e.g. MySQL or MariaDB
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified database (any version) in that month.
	 * - percentage - the relative frequency of mentions of the specified database (any version) in that month.
	 */
	public const DB_TYPE = "DB_TYPE";

	/**
	 * Monthly stats for database drivers
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - db_driver     - the driver, e.g. mysqli
	 * - db_type       - the name of the database, e.g. MySQL or MariaDB
	 * - db_connection - the type of connection, one of PDO or Native
	 * - year          - the year of the recording
	 * - month         - the month of the recording
	 * - count         - the absolute number of mentions of the specified database (any version) in that month.
	 * - percentage    - the relative frequency of mentions of the specified database (any version) in that month.
	 */
	public const DB_DRIVER = "DB_DRIVER";

	/**
	 * Monthly stats for PHP versions grouped by CMS version
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - cms_short  - the short version of the CMS, e.g. major.minor
	 * - php_short  - the short version of PHP, e.g. major.minor
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified version in that month.
	 * - percentage - the relative frequency of mentions of the specified version in that month.
	 */
	public const PHP_VERSION_BY_CMS = "PHP_VERSION_BY_CMS";

	/**
	 * Monthly stats for database versions grouped by CMS version
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - cms_short  - the short version of the CMS, e.g. major.minor
	 * - db_type    - the name of the database, e.g. MySQL or MariaDB
	 * - db_short   - the short version of PHP, e.g. major.minor
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified version in that month.
	 * - percentage - the relative frequency of mentions of the specified version in that month.
	 */
	public const DB_VERSION_BY_CMS = "DB_VERSION_BY_CMS";

	/**
	 * Monthly stats for operating systems
	 *
	 * Each record of this aspect is an associative array with the keys
	 * - platform   - the name of the operating system, e.g. Linux or Windows
	 * - year       - the year of the recording
	 * - month      - the month of the recording
	 * - count      - the absolute number of mentions of the specified database (any version) in that month.
	 * - percentage - the relative frequency of mentions of the specified database (any version) in that month.
	 */
	public const PLATFORM = "PLATFORM";

	private $value;

	public function __construct($value)
	{
		$this->value = $value;
	}

	public function __get($property)
	{
		if ($property === 'name')
		{
			return strtolower($this->value);
		}

		throw(new \RuntimeException("Property $property not found in " . self::class));
	}

	/**
	 * @return string[]
	 */
	public function columns(): array
	{
		static $values = [
				Aspect::REPORTS            => [],
				Aspect::CMS_VERSION        => ['cms_short'],
				Aspect::PHP_VERSION        => ['php_short'],
				Aspect::DB_VERSION         => ['db_type', 'db_short'],
				Aspect::DB_TYPE            => ['db_type'],
				Aspect::DB_DRIVER          => ['db_driver', 'db_type', 'db_connection'],
				Aspect::PHP_VERSION_BY_CMS => ['cms_short', 'php_short'],
				Aspect::DB_VERSION_BY_CMS  => ['cms_short', 'db_type', 'db_short'],
				Aspect::PLATFORM           => ['platform'],

		];

		return $values[$this->value];
	}

	/**
	 * @return string
	 */
	public function label(): string
	{
		static $values = [
				Aspect::REPORTS            => 'Reports',
				Aspect::CMS_VERSION        => 'CMS Versions',
				Aspect::PHP_VERSION        => 'PHP Versions',
				Aspect::DB_VERSION         => 'Database Versions',
				Aspect::DB_TYPE            => 'Databases',
				Aspect::DB_DRIVER          => 'Database Drivers',
				Aspect::PHP_VERSION_BY_CMS => 'PHP Versions per CMS Version',
				Aspect::DB_VERSION_BY_CMS  => 'Database Versions per CMS Version',
				Aspect::PLATFORM           => 'Operating Systems',
		];

		return $values[$this->value];
	}
}
