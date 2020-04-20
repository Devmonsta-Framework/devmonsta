<?php if ( ! defined( 'DMS' ) ) {
	die( 'Forbidden' );
}
/**
 * @var  DMS_Option_Type_Typography_v2 $typography_v2
 * @var  string $id
 * @var  array $option
 * @var  array $data
 * @var array $defaults
 */

{
	$wrapper_attr = $option['attr'];

	unset(
		$wrapper_attr['value'],
		$wrapper_attr['name']
	);
}

{
	$option['value'] = array_merge( $defaults['value'], (array) $option['value'] );
	$data['value']   = array_merge( $option['value'], is_array($data['value']) ? $data['value'] : array() );
	$google_font     = $typography_v2->get_google_font( $data['value']['family'] );

}

$components = (isset($option['components']) && is_array($option['components']))
	? array_merge($defaults['components'], $option['components'])
	: $defaults['components'];
?>
<div <?php echo dms_attr_to_html( $wrapper_attr ) ?>>
	<?php if ( $components['family'] ) : ?>
		<div class="dms-option-typography-v2-option dms-option-typography-v2-option-family dms-border-box-sizing dms-col-sm-5">
			<select data-type="family" data-value="<?php echo esc_attr($data['value']['family']); ?>"
			        name="<?php echo esc_attr( $option['attr']['name'] ) ?>[family]"
			        class="dms-option-typography-v2-option-family-input">
			</select>

			<div class="dms-inner"><?php _e('Font face', 'dms'); ?></div>
		</div>

		<?php if ( $components['style'] ) : ?>
		<div class="dms-option-typography-v2-option dms-option-typography-v2-option-style dms-border-box-sizing dms-col-sm-3"
		     style="display: <?php echo ( $google_font ) ? 'none' : 'inline-block'; ?>;">
			<select data-type="style" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[style]"
			        class="dms-option-typography-v2-option-style-input">
				<?php foreach (
					array(
						'normal'  => __('Normal', 'dms'),
						'italic'  => __('Italic', 'dms'),
						'oblique' => __('Oblique', 'dms')
					)
					as $key => $style
				): ?>
					<option value="<?php echo esc_attr( $key ) ?>"
					        <?php if ($data['value']['style'] === $key): ?>selected="selected"<?php endif; ?>><?php echo dms_htmlspecialchars( $style ) ?></option>
				<?php endforeach; ?>
			</select>

			<div class="dms-inner"><?php _e( 'Style', 'dms' ); ?></div>
		</div>
		<?php endif; ?>

		<?php if ( $components['weight'] ) : ?>
		<div class="dms-option-typography-v2-option dms-option-typography-v2-option-weight dms-border-box-sizing dms-col-sm-3"
		     style="display: <?php echo ( $google_font ) ? 'none' : 'inline-block'; ?>;">
			<select data-type="weight" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[weight]"
			        class="dms-option-typography-v2-option-weight-input">
				<?php foreach (
					array(
						100 => 100,
						200 => 200,
						300 => 300,
						400 => 400,
						500 => 500,
						600 => 600,
						700 => 700,
						800 => 800,
						900 => 900
					)
					as $key => $style
				): ?>
					<option value="<?php echo esc_attr( $key ) ?>"
					        <?php if ($data['value']['weight'] == $key): ?>selected="selected"<?php endif; ?>><?php echo dms_htmlspecialchars( $style ) ?></option>
				<?php endforeach; ?>
			</select>

			<div class="dms-inner"><?php _e( 'Weight', 'dms' ); ?></div>
		</div>
		<?php endif; ?>

		<div class="dms-option-typography-v2-option dms-option-typography-v2-option-subset dms-border-box-sizing dms-col-sm-2"
		     style="display: <?php echo ( $google_font ) ? 'inline-block' : 'none'; ?>;">
			<select data-type="subset" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[subset]"
			        class="dms-option-typography-v2-option-subset">
				<?php if ( $google_font ) {
					foreach ( $google_font['subsets'] as $subset ) { ?>
						<option value="<?php echo esc_attr( $subset ) ?>"
						        <?php if ($data['value']['subset'] === $subset): ?>selected="selected"<?php endif; ?>><?php echo dms_htmlspecialchars( $subset ); ?></option>
					<?php }
				}
				?>
			</select>

			<div class="dms-inner"><?php _e( 'Script', 'dms' ); ?></div>
		</div>


		<?php if ( $components['variation'] ) : ?>
		<div
			class="dms-option-typography-v2-option dms-option-typography-v2-option-variation dms-border-box-sizing dms-col-sm-2"
			style="display: <?php echo ( $google_font ) ? 'inline-block' : 'none'; ?>;">
			<select data-type="variation" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[variation]"
			        class="dms-option-typography-v2-option-variation">
				<?php if ( $google_font ) {
					foreach ( $google_font['variants'] as $variant ) { ?>
						<option value="<?php echo esc_attr( $variant ) ?>"
						        <?php if ($data['value']['variation'] == $variant): ?>selected="selected"<?php endif; ?>><?php echo dms_htmlspecialchars( $variant ); ?></option>
					<?php }
				}
				?>
			</select>

			<div class="dms-inner"><?php esc_html_e( 'Style', 'dms' ); ?></div>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $components['size'] ) : ?>
		<div class="dms-option-typography-v2-option dms-option-typography-v2-option-size dms-border-box-sizing dms-col-sm-2">
			<input data-type="size" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[size]"
			       class="dms-option-typography-v2-option-size-input" type="text"
			       value="<?php echo esc_attr($data['value']['size']); ?>">

			<div class="dms-inner"><?php esc_html_e( 'Size', 'dms' ); ?></div>
		</div>
	<?php endif; ?>

	<?php if ( $components['line-height'] ) : ?>
		<div
			class="dms-option-typography-v2-option dms-option-typography-v2-option-line-height dms-border-box-sizing dms-col-sm-2">
			<input data-type="line-height" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[line-height]"
			       value="<?php echo esc_attr($data['value']['line-height']); ?>"
			       class="dms-option-typography-v2-option-line-height-input" type="text">

			<div class="dms-inner"><?php esc_html_e( 'Line height', 'dms' ); ?></div>
		</div>
	<?php endif; ?>

	<?php if ( $components['letter-spacing'] ) : ?>
		<div
			class="dms-option-typography-v2-option dms-option-typography-v2-option-letter-spacing dms-border-box-sizing dms-col-sm-2">
			<input data-type="letter-spacing" name="<?php echo esc_attr( $option['attr']['name'] ) ?>[letter-spacing]"
			       value="<?php echo esc_attr($data['value']['letter-spacing']); ?>"
			       class="dms-option-typography-v2-option-letter-spacing-input" type="text">

			<div class="dms-inner"><?php esc_html_e( 'Spacing', 'dms' ); ?></div>
		</div>
	<?php endif; ?>

	<?php if ( $components['color'] ) : ?>
		<div class="dms-option-typography-v2-option dms-option-typography-v2-option-color dms-border-box-sizing dms-col-sm-2"
		     data-type="color">
			<?php
			echo dms()->backend->option_type( 'color-picker' )->render(
				'color',
				array(
					'label' => false,
					'desc'  => false,
					'type'  => 'color-picker',
					'value' => $option['value']['color']
				),
				array(
					'value'       => $data['value']['color'],
					'id_prefix'   => 'dms-option-' . $id . '-typography-v2-option-',
					'name_prefix' => $data['name_prefix'] . '[' . $id . ']',
				)
			)
			?>
			<div class="dms-inner"><?php esc_html_e( 'Color', 'dms' ); ?></div>
		</div>
	<?php endif; ?>

</div>
