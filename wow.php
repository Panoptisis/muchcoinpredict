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

// We use cached data because we're nice shibes
$result = apc_fetch('muchcoinpredict_results');
if (!$result)
{
	$payload = file_get_contents('http://dogechain.info/chain/Dogecoin/get_blocks');
	$payload = json_decode($payload, true);
	$block = $payload['aaData'][0];
	$height = $block[0] + 1;
	$hash = $block[8];
	$value = getBlockValue($height, $hash);

	$result = json_encode([
		'height'      => $height,
		'hash'        => $hash,
		'value'       => $value,
		'prettyValue' => number_format($value, 2),
	]);
	apc_add('muchcoinpredict_results', $result, 30);
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');
echo $result;
