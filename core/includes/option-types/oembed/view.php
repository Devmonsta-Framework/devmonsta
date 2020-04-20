<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}

$wrapper_attr                 = $option['attr'];
$wrapper_attr['data-nonce']   = wp_create_nonce( '_action_get_oembed_response' );
$wrapper_attr['data-preview'] = json_encode( $option['preview'] );

unset(
	$wrapper_attr['value'],
	$wrapper_attr['name'],
	$wrapper_attr['placeholder']
);

$input_attr['value']       = $data['value'];
$input_attr['name']        = $option['attr']['name'];
$input_attr['placeholder'] = $option['attr']['placeholder'];
?>
<div <?php echo dms_attr_to_html( $wrapper_attr ) ?>>

	<div class="dms-oembed-input">
		<input type="text" <?php echo dms_attr_to_html( $input_attr ); ?>/>
	</div>
	<div class="dms-oembed-preview">
		<?php
		$value = dms_akg('value', $data);
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			$iframe = empty( $option['preview']['keep_ratio'] ) ? dms_oembed_get( $value, array(
				'height' => $option['preview']['height'],
				'width'  => $option['preview']['width']
			) ) :
				wp_oembed_get( $value, array(
					'height' => $option['preview']['height'],
					'width'  => $option['preview']['width']
				) );

			echo DMS_Helpers::render($iframe);
		}
		?>
	</div>
</div>