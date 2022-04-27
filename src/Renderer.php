<?php

namespace Joomla\Statistics;

/**
 * Render distributions or time series
 */
class Renderer
{
	/**
	 * Render a distribution
	 *
	 * @example
	 * cms_short, year, month, count, percentage
	 *      3.10, 2022,     1, 28998, 53.2327
	 *       3.3, 2022,     1,     1,  0.0018
	 *       3.4, 2022,     1,     6,  0.0110
	 *       3.5, 2022,     1,   247,  0.4534
	 *       3.6, 2022,     1,  1078,  1.9789
	 *       3.7, 2022,     1,   325,  0.5966
	 *       3.8, 2022,     1,   895,  1.6430
	 *       3.9, 2022,     1,  9691, 17.7901
	 *       4.0, 2022,     1, 13136, 24.1143
	 *       4.1, 2022,     1,    97,  0.1781
	 *
	 * @param   Aspect  $aspect
	 * @param   array   $data
	 *
	 * @return string
	 */
	public function renderDistribution(Aspect $aspect, array $data): string
	{
		$columns = $aspect->columns();

		if (count($columns) === 0)
		{
			throw new \RuntimeException('Cannot generate distribution chart for a single value');
		}

		if (count($columns) === 1)
		{
			[$title, $subtitle, $chartData] = $this->prepareChartData($data, reset($columns), $aspect);

			return $this->generateDoughnut($aspect->name, $title, $subtitle, $chartData);
		}

		if (count($columns) === 2 && in_array('cms_short', $columns))
		{
			// Grouped by CMS version
			unset($columns['cms_short']);

			$cmsData = [];
			foreach ($data as $row)
			{
				$cmsVersion             = $row['cms_short'];
				$cmsData[$cmsVersion][] = $row;
			}

			uksort(
					$cmsData,
					'version_compare'
			);

			foreach ($cmsData as $version => $row)
			{
				[$title, $subtitle, $chartData[$version]] = $this->prepareChartData($data, reset($columns), $aspect);
			}
		}
	}

	private function groupByMonth(array $data, string $index): array
	{
		$result = [];

		foreach ($data as $row)
		{
			$result[$row['year']][$row['month']][$row[$index]] = [
					'count'      => $row['count'],
					'percentage' => $row['percentage'],
			];
		}

		return $result;
	}

	/**
	 * @throws \JsonException
	 */
	private function generateDoughnut(string $id, string $title, string $subtitle, array $data): string
	{
		$labels  = '"' . implode('","', $data['labels']) . '"';
		$values  = implode(',', $data['values']);
		$colors  = '"' . implode('","', $data['colors']) . '"';
		$borders = '"' . implode('","', array_fill(0, count($data['values']), '#FFFFFF')) . '"';

		ob_start();
		include __DIR__ . '/templates/doughnut.php';

		return ob_get_clean();
	}

	/**
	 * @param   array  $data
	 *
	 * @return void
	 */
	private function assertOnlyOneElement(array $data): void
	{
		if (count($data) !== 1)
		{
			throw new \RuntimeException('Invalid data: multiple elements');
		}
	}

	private function extractKeyAndValue($data)
	{
		$this->assertOnlyOneElement($data);

		$value = reset($data);
		$key   = key($data);

		return [$key, $value];
	}

	/**
	 * @param   array    $data
	 * @param   string   $column
	 * @param   Aspect  $aspect
	 *
	 * @return array
	 */
	private function prepareChartData(array $data, string $column, Aspect $aspect): array
	{
		#echo '<pre>'.__METHOD__ . ": Processing $column for " . print_r($data, true) . '</pre>';
		$data = $this->groupByMonth($data, $column);
		[$year, $data] = $this->extractKeyAndValue($data);
		[$month, $data] = $this->extractKeyAndValue($data);

		$title    = $aspect->label();
		$subtitle = date('F Y', mktime(0, 0, 0, $month, 1, $year));

		uksort(
				$data,
				'version_compare'
		);

		$angle = 0;
		$chartData = [];
		foreach ($data as $version => $value)
		{
			$hue = $angle + $value['percentage'] / 100 * 360 / 2;
			$angle += $value['percentage'] / 100 * 360;
			$chartData['labels'][] = $version ?: 'unknown';
			$chartData['values'][] = $value['percentage'];
			$chartData['colors'][] = $this->hsvToRgb(fmod($hue, 360), 1.0, 0.8);
		}

		return array($title, $subtitle, $chartData);
	}

	private function hsvToRgb(float $h, float $s, float $v)
	{
		$sextant = $h / 60;
		$c       = $v * $s;
		$x       = $c * (1 - abs(fmod($sextant, 2) - 1));
		$m       = $v - $c;
		$map     = [
				0 => [$c, $x, 0],
				1 => [$x, $c, 0],
				2 => [0, $c, $x],
				3 => [0, $x, $c],
				4 => [$x, 0, $c],
				5 => [$c, 0, $x],
		];
		$sextant = floor($sextant);
		[$r, $g, $b] = $map[($sextant)];

		return sprintf('#%02x%02x%02x', ($r + $m) * 255, ($g + $m) * 255, ($b + $m) * 255);
	}
}
