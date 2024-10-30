<?php
/*
Plugin Name:   Cryptocoin Live Ticker
Description:   This widget displays cryptocoins current price, 24 hours price change and 7 days price change.
Version:       1.5.2
Author:        Coinalyze
Author URI:    https://coinalyze.net
License: GPL2 or later
*/
require_once( dirname( __FILE__ ) . '/include/common.php' );

class CryptocoinLiveTickerWidget extends WP_Widget {

	function CryptocoinLiveTickerWidget() {
    	$widget_options = array( 
      		'classname' => 'CryptocoinLiveTickerWidget',
      		'description' => 'Cryptocoin Live Ticker',
    	);
    	parent::__construct('CryptocoinLiveTickerWidget', 'Cryptocoin Live Ticker', $widget_options );
  	}


	public function widget( $args, $instance ) {
		$pairs = explode(',', $instance['pairs']);
                $tableHeaders = explode(',', $instance['table_headers']);
                
                $decimalsSep = $instance['decimals_sep'];
                $thousandsSep = $instance['thousands_sep'];
                
                
		$cacheDataTimeout = $instance['cache_data_timeout'];
		$pairsData = clt_get_pair_data($pairs, $cacheDataTimeout);
		?>
		<div class="cryptocoin-live-ticker">
			<div class="pairs-data">
				<div class="pairs-header">
					<div><?php echo $tableHeaders[0] ?></div>
					<div><?php echo $tableHeaders[1] ?></div>
					<div><?php echo $tableHeaders[2] ?></div>
					<div><?php echo $tableHeaders[3] ?></div>
				</div>
				<?php 
				foreach ($pairs as $idx => $pair) {
					$pairData = $pairsData[$pair];
					if(empty($pairData)) {
						continue;
					}
					$pchange_24hoursClass = '';
					$pchange_7daysClass = '';

					if($pairData['pchange_24hours'] > 0) {
						$pchange_24hoursClass = ' green-color';
					} else if($pairData['pchange_24hours'] < 0) {
						$pchange_24hoursClass = ' red-color';
					}

					if($pairData['pchange_7days'] > 0) {
						$pchange_7daysClass = ' green-color';
					} else if($pairData['pchange_7days'] < 0) {
						$pchange_7daysClass = ' red-color';
					}
                                        
                                        $priceDeciamls = 2;
                                        if($pairData['price'] < 1) {
                                            if($pairData['price'] < 0.00001) {
                                                $priceDeciamls = 6;
                                            } else {
                                                $priceDeciamls = 5;
                                            }
                                            
                                        }
					?>
					<div class="pair-data" id="pair-<?php echo $pair ?>">
						<div class="pair"><?php echo $pair ?></div>
						<div class="price"><?php echo number_format($pairData['price'], $priceDeciamls, $decimalsSep, $thousandsSep) ?></div>
						<div class="pchange-24hours<?php echo $pchange_24hoursClass ?>"><?php echo ($pairData['pchange_24hours'] > 0 ? '+' : '') . number_format($pairData['pchange_24hours'], 2, $decimalsSep, $thousandsSep) ?></div>
						<div class="pchange-7days<?php echo $pchange_7daysClass ?>"><?php echo ($pairData['pchange_7days'] > 0 ? '+' : '') . number_format($pairData['pchange_7days'], 2, $decimalsSep, $thousandsSep) ?></div>
					</div>
				<?php
				} 
				?>
			</div>
			<?php if($instance['show_coinalyze_link'] === 'on') { ?>
			<div class="provided-by">Powered by <a href="https://coinalyze.net/">Coinalyze</a></div>
			<?php } ?>
		</div>
		<?php
	}

	public function form( $instance ) {
		$pairs = !empty( $instance['pairs'] ) ? $instance['pairs'] : 'BTCUSD,LTCUSD,ETHUSD';
                $tableHeaders = !empty( $instance['table_headers'] ) ? $instance['table_headers'] : 'Pair,Rate,24H %,7D %';
                $decimalsSep = isset( $instance['decimals_sep'] ) ? $instance['decimals_sep'] : '.';
                $thousandsSep = isset( $instance['thousands_sep'] ) ? $instance['thousands_sep'] : ',';
                
		$cacheDataTimeout = !empty( $instance['cache_data_timeout'] ) ? $instance['cache_data_timeout'] : $GLOBALS['cltConfig']['cacheDataTimeout'];
		$showCoinalyzeLink = !empty($instance['show_coinalyze_link']) ? $instance['show_coinalyze_link'] : 'on';
		?>
 		<p>
                    <label for="<?php echo $this->get_field_id( 'pairs' ); ?>">Pairs to display, comma separated.</label> <span style="vertical-align: middle">Available pairs:</span> <span style="font-size: 0.85em;"><?php echo implode(' ', $GLOBALS['cltConfig']['availablePairs']) ?></span>
                    <input style="width:100%;" type="text" id="<?php echo $this->get_field_id( 'pairs' ); ?>" name="<?php echo $this->get_field_name( 'pairs' ); ?>" value="<?php echo esc_attr( $pairs ); ?>" />
  		</p>
 		<p>
                    <label for="<?php echo $this->get_field_id( 'table_headers' ); ?>">Header names, comma separated</label>
                    <input style="width:100%;" type="text" id="<?php echo $this->get_field_id( 'table_headers' ); ?>" name="<?php echo $this->get_field_name( 'table_headers' ); ?>" value="<?php echo esc_attr( $tableHeaders ); ?>" />
  		</p>
                
                <p>
                    <div style="display: inline-block; width: 45%;">
                        <label for="<?php echo $this->get_field_id( 'decimals_sep' ); ?>">Decimals separator</label>
                        <input style="width:100%;" type="text" id="<?php echo $this->get_field_id( 'decimals_sep' ); ?>" name="<?php echo $this->get_field_name( 'decimals_sep' ); ?>" value="<?php echo esc_attr( $decimalsSep ); ?>" />
                    </div>
                    <div style="display: inline-block; width: 45%; float: right;">
                        <label for="<?php echo $this->get_field_id( 'thousands_sep' ); ?>">Thousands separator</label>
                        <input style="width:100%;" type="text" id="<?php echo $this->get_field_id( 'thousands_sep' ); ?>" name="<?php echo $this->get_field_name( 'thousands_sep' ); ?>" value="<?php echo esc_attr( $thousandsSep ); ?>" />
                    </div>
                </p>
                
 		<p>
                    <label for="<?php echo $this->get_field_id( 'cache_data_timeout' ); ?>">Cached data timeout in seconds, not lower than <?php echo $GLOBALS['cltConfig']['cacheDataTimeout'] ?></label>
                    <input style="width:100%;" type="text" id="<?php echo $this->get_field_id( 'cache_data_timeout' ); ?>" name="<?php echo $this->get_field_name( 'cache_data_timeout' ); ?>" value="<?php echo esc_attr( $cacheDataTimeout ); ?>" />
  		</p>
		<p>
		    <label for="<?php echo $this->get_field_id( 'show_coinalyze_link' ); ?>">Show coinalyze.net link ? </label>
		    <input class="checkbox" type="checkbox" <?php checked($showCoinalyzeLink, 'on'); ?> id="<?php echo $this->get_field_id( 'show_coinalyze_link' ); ?>" name="<?php echo $this->get_field_name( 'show_coinalyze_link' ); ?>" /> 
		</p>  		
		<?php	
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
  		$instance['pairs'] = str_replace(' ', '', $new_instance['pairs']);
                $instance['table_headers'] = $new_instance['table_headers'];
                $instance['decimals_sep'] = $new_instance['decimals_sep'];
                $instance['thousands_sep'] = $new_instance['thousands_sep'];
                
  		$instance['show_coinalyze_link'] = empty($new_instance['show_coinalyze_link']) ? 'off' : 'on';
  		$cacheDataTimeout = (int)$new_instance['cache_data_timeout'];
  		if($cacheDataTimeout < $GLOBALS['cltConfig']['cacheDataTimeout']) {
  			$cacheDataTimeout = $GLOBALS['cltConfig']['cacheDataTimeout'];
  		}

  		$instance['cache_data_timeout'] = $cacheDataTimeout;

  		delete_transient('cltPairsDataCache');
  		return $instance;		
	}

}


// ACTION HANDLERS
function clt_register_widget() { 
  register_widget( 'CryptocoinLiveTickerWidget' );
}

add_action( 'widgets_init', 'clt_register_widget' );
wp_enqueue_style( 'cryptocoin-live-ticker-css', plugins_url( '/assets/style.css', __FILE__ ));
?>