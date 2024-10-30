<?php
$GLOBALS['cltConfig'] = array(
	'availablePairs' => array('BTCUSD', 'BTCEUR', 'BTCZAR', 'BTCNZD', 'BTCINR', 'LTCUSD', 'LTCEUR', 'LTCBTC', 'BCHUSD', 'BCHEUR', 'BCHBTC', 'ETHUSD', 'ETHEUR', 'ETHBTC', 'ETCUSD', 'XMRUSD', 'XMREUR', 'XRPUSD', 'XRPEUR', 'IOTUSD', 'DSHUSD', 'NEOUSD', 'BTCJPY', 'ETHJPY', 'BTCGBP', 'BTCCAD', 'ETHCAD', 'BTCAUD', 'ETHAUD', 'LTCAUD', 'ZECUSD', 'OMGUSD', 'XLMUSD', 'BTCKRW', 'BCHKRW', 'LTCKRW', 'ETHKRW', 'XMRKRW', 'XRPKRW', 'DSHKRW', 'ZECKRW', 'ETCKRW', 'QTUMKRW', 'EOSUSD', 'EOSKRW', 'EOSBTC', 'EOSETH', 'ADAUSD', 'ADABTC', 'ADAETH', 'BATUSD', 'BATBTC', 'BATETH', 'GNTUSD', 'GNTBTC', 'GNTETH', 'TRXUSD', 'TRXBTC', 'TRXETH', 'NANOBTC', 'NANOETH', 'XVGUSD', 'XVGBTC', 'XVGETH', 'ONTBTC', 'ONTETH', 'POWRBTC', 'POWRETH'),
	'cacheDataTimeout' => 30,
	'dataApiUrl' => 'https://coinalyze.net/service-widgets/pairsdata/'
);


function clt_pairs_no_data($pairs) {
	$pairsData = array();
	foreach ($pairs as $idx => $pair) {
		$pairsData[$pair] = array(
			'price' => 0,
			'pchange_24hours' => 0,
			'pchange_7days' => 0
		);
	}

	return $pairsData;
}

function clt_get_pair_data($pairs, $cacheDataTimeout) {
	$cacheKey = 'cltPairsDataCache';
	$pairsData = get_transient($cacheKey);
	if($pairsData === false) {

		$response = wp_remote_post($GLOBALS['cltConfig']['dataApiUrl'], array(
			'method' => 'POST',
			'timeout' => 10,
			'body' => array('pairs' => implode(',', $pairs), 'url' => get_site_url())
		    )
		);

		if( is_wp_error( $response) ) {
			return clt_pairs_no_data($pairs);
		}


		$body = json_decode($response['body'], true);

		if($body === null || $body['error'] === true) {
			return clt_pairs_no_data($pairs);	
		}

		$pairsData = $body['data'];
		set_transient($cacheKey, $pairsData, $cacheDataTimeout);
	}

	return $pairsData;
}
?>