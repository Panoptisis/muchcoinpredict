<?php

require 'mersenne_twister.php';
use mersenne_twister\twister;

/**
 * Generates a random value using the Mersenne Twister method such that is more closely
 * agrees with values produced by Boost's C++ libraries.
 *
 * @param  int $seed  The value to seed the random generator with
 * @param  int $range The maximum value this random number can be
 * @return int        The random number
 */
function generateRand($seed, $range)
{
	$twister = new twister($seed);
	return $twister->rangereal_open(1, $range);
}

/**
 * Gets the block value for the specified height and hash.
 *
 * @param  int    $height       The height to find
 * @param  string $previousHash The hash of the previous block
 * @return int                  The (approximate) value of the next block
 */
function getBlockValue($height, $previousHash)
{
	if ($height < 100000)
	{
		$seed = hexdec(substr($previousHash, 7, 7));
		$rand = generateRand($seed, 999999);
	}
	else if ($height < 200000)
	{
		$seed = hexdec(substr($previousHash, 7, 7));
		$rand = generateRand($seed, 499999);
	}
	else if ($height < 300000)
	{
		$seed = hexdec(substr($previousHash, 6, 7));
		$rand = generateRand($seed, 249999);
	}
	else if ($height < 400000)
	{
		$seed = hexdec(substr($previousHash, 7, 7));
		$rand = generateRand($seed, 124999);
	}
	else if ($height < 500000)
	{
		$seed = hexdec(substr($previousHash, 7, 7));
		$rand = generateRand($seed, 62499);
	}
	else if ($height < 600000)
	{
		$seed = hexdec(substr($previousHash, 6, 7));
		$rand = generateRand($seed, 31249);
	}
	else
	{
		$rand = 10000 - 1; // Offset the + 1 in the return
	}

	return 1 + $rand;
}

/**
 * Gets the actual value of a hash if it's available.
 *
 * @param  string         $hash   The hash of the block to look up 
 * @return string|boolean         The actual amount produced by this block, or 
 */
function getActualBlockValue($hash)
{
	$html = file_get_contents('http://dogechain.info/block/'. $hash);
	if (preg_match('@Generation: (\d+(?:\.\d+)?)@', $html, $matches))
	{
		return $matches[1];
	}

	return false;
}

// We use cached data because we're nice shibes
$result = apc_fetch('muchcoinpredict_results');
if (!$result)
{
	$payload = file_get_contents('http://dogechain.info/chain/Dogecoin/get_blocks');
	$payload = json_decode($payload, true);
	$blocks = $payload['aaData'];
	$block = $blocks[0];
	$height = $block[0] + 1;
	$hash = $block[8];
	$value = getBlockValue($height, $hash);

	// Let's make history (if we need to)
	$history = [];
	for ($i = count($blocks) - 2; $i >= 0; $i--)
	{
		$currentBlock = $blocks[$i];
		$previousBlock = $blocks[$i + 1];
		$block = apc_fetch('muchcoinpredict_history_'. $currentBlock[0]);
		if (!$block)
		{
			$actualValue = getActualBlockValue($currentBlock[8]);
			$predictedValue = getBlockValue($currentBlock[0], $previousBlock[8]);
			if ($actualValue)
			{
				$block = [
					'height'         => $currentBlock[0],
					'hash'           => $currentBlock[8],
					'predictedValue' => number_format($predictedValue, 2),
					'actualValue'    => number_format($actualValue, 2),
					'diff'           => number_format(abs($actualValue - $predictedValue), 2),
				];
				apc_add('muchcoinpredict_history_'. $currentBlock[0], $block, 300);
			}
		}

		if ($block)
		{
			$history[] = $block;
		}
	}

	$result = json_encode([
		'height'      => $height,
		'hash'        => $hash,
		'value'       => $value,
		'prettyValue' => number_format($value, 2),
		'history'     => $history,
	]);
	apc_add('muchcoinpredict_results', $result, 30);
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');
echo $result;
