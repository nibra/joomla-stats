<?php

namespace Joomla\Statistics;

/**
 * Extract distributions or time series from the statistic data
 */
class Model
{
	/**
	 * @var \PDO
	 */
	private $databaseDriver;

	/**
	 * @param   \PDO  $databaseDriver
	 */
	public function __construct(\PDO $databaseDriver)
	{
		$this->databaseDriver = $databaseDriver;
	}

	/**
	 * Get the distribution for an aspect in a specific month
	 *
	 * See Aspect for the returned structure.
	 *
	 * @param   Aspect  $aspect  The aspect to get the distribution for
	 * @param   int     $year    The year of the timeframe
	 * @param   int     $month   The month of the timeframe
	 *
	 * @return array|null
	 */
	public function getDistribution(Aspect $aspect, int $year, int $month): ?array
	{
		return $this->getList(
			$aspect,
			sprintf("WHERE a.`year`=%d AND a.`month`=%d ", $year, $month)
		);
	}

	/**
	 * Get the time series for an aspect from a specific month until today
	 *
	 * See Aspect for the returned structure.
	 *
	 * @param   Aspect  $aspect     The aspect to get the time series for
	 * @param   int     $fromYear   The first year of the timeframe
	 * @param   int     $fromMonth  The first month in the given year of the timeframe
	 *
	 * @return array|null
	 */
	public function getTimeSeries(Aspect $aspect, int $fromYear, int $fromMonth): ?array
	{
		return $this->getList(
			$aspect,
			sprintf("WHERE a.`year_month`>='%04d.%02d' ", $fromYear, $fromMonth)
		);
	}

	/**
	 * @param $columns
	 *
	 * @return string
	 */
	private function createScopedFieldList($columns): string
	{
		return array_reduce(
				   $columns,
				   static function ($carry, $column) {
					   $carry .= "a.`$column`, ";

					   return $carry;
				   },
				   ''
			   ) . "a.`year`, a.`month`";
	}

	/**
	 * @param   string[]  $columns  The requested columns
	 *
	 * @return string
	 */
	private function createAggregationWindow(array $columns): string
	{
		$window = "b.`year_month`=a.`year_month`";

		if (count($columns) > 1)
		{
			foreach (['cms_short', 'cms_major', 'cms_version'] as $version)
			{
				if (in_array($version, $columns, true))
				{
					$window .= " AND b.`{$version}`=a.`{$version}`";
					break;
				}
			}
		}

		return $window;
	}

	/**
	 * @param   string[]  $columns  The requested columns
	 * @param   string    $condition
	 *
	 * @return void
	 */
	private function buildSQLQuery(array $columns, string $condition): string
	{
		static $denormalisedData = null;

		if ($denormalisedData === null)
		{
			$denormalisedData = file_get_contents(__DIR__ . '/details.sql');
		}

		$fields = $this->createScopedFieldList($columns);

		$percentage = '';
		if (count($columns) > 0)
		{
			$window     = $this->createAggregationWindow($columns);
			$percentage = ",\n  COUNT(*)*100/(SELECT COUNT(*) FROM `details` AS b WHERE {$window}) AS `percentage`";
		}

		return "WITH `details` AS (\n    " . $denormalisedData . "\n)"
			   . "\nSELECT " . $fields . ",\n  COUNT(*) AS `count`" . $percentage
			   . "\n  FROM `details` AS a "
			   . "\n" . $condition
			   . "\nGROUP BY " . $fields;
	}

	/**
	 * @param   Aspect  $aspect
	 * @param   string  $condition
	 *
	 * @return array|null
	 */
	private function getList(Aspect $aspect, string $condition): ?array
	{
		$query = $this->buildSQLQuery($aspect->columns(), $condition);

		$statement = $this->databaseDriver->query($query);
		if ($statement === false) {
			$info = implode("\n", $this->databaseDriver->errorInfo());
			$info .= "\n" . $query;
			echo "<pre>Error: {$info}</pre>";
		}

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}
}
