<?php if (!defined('DMS')) die('Forbidden');
/**
 * @var string $id
 * @var  array $option
 * @var  array $data
 * @var  array $controls
 * @var  array $box_options
 */

$attr = $option['attr'];
unset($attr['name']);
unset($attr['value']);

// generate controls html
{
	ob_start(); ?>
	<small class="dms-option-box-controls">
		<?php foreach ($controls as $c_id => $control): ?>
			<small class="dms-option-box-control-wrapper"><a href="#" class="dms-option-box-control" data-control-id="<?php echo esc_attr($c_id) ?>" onclick="return false"><?php echo esc_html($control) ?></a></small>
		<?php endforeach; ?>
	</small>
	<?php $controls_html = ob_get_clean();
}

if ($option['sortable']) {
	$attr['class'] .= ' is-sortable';
}

$attr['class'] .= ' width-type-'. $option['width'];

if (!empty($data['value'])) {
	$attr['class'] .= ' has-boxes';
}
?>
<div <?php echo dms_attr_to_html($attr); ?>>
	
	<?php echo dms()->backend->option_type('hidden')->render($id, array('value' => '~'), array(
		'id_prefix' => $data['id_prefix'],
		'name_prefix' => $data['name_prefix'],
	)); ?>
	<?php $i = 0; ?>
	<div class="dms-option-boxes metabox-holder">
		<?php foreach ($data['value'] as $value_index => &$values): ?>
			<?php $i++; ?>
			<div class="dms-option-box dms-backend-options-virtual-context" data-name-prefix="<?php echo dms_htmlspecialchars($data['name_prefix'] .'['. $id .']['. $i .']') ?>" data-values="<?php echo dms_htmlspecialchars(json_encode($values)) ?>">
				<?php ob_start() ?>
				<div class="dms-option-box-options dms-force-xs">
					<?php
					echo dms()->backend->render_options($box_options, $values, array(
						'id_prefix'   => $data['id_prefix'] . $id .'-'. $i .'-',
						'name_prefix' => $data['name_prefix'] .'['. $id .']['. $i .']',
					));
					?>
				</div>
				<?php
				echo dms()->backend->render_box(
					$data['id_prefix'] . $id .'-'. $i .'-box',
					'&nbsp;',
					ob_get_clean(),
					array(
						'html_after_title' => $controls_html,
						'attr' => array(
							'class' => 'dms-option-type-addable-box-pending-title-update',
						),
					)
				);
				?>
			</div>
		<?php endforeach; unset($values); ?>
	</div>
	<br class="default-box-template dms-hidden" data-template="<?php
		/**
		 * Place template in attribute to prevent it to be treated as html
		 * when this option will be used inside another option template
		 */

		$values = array();

		// must contain characters that will remain the same after htmlspecialchars()
		$increment_placeholder = '###-addable-box-increment-'. dms_rand_md5() .'-###';

		echo dms_htmlspecialchars(
			'<div class="dms-option-box dms-backend-options-virtual-context" data-name-prefix="'. dms_htmlspecialchars($data['name_prefix'] .'['. $id .']['. $increment_placeholder .']') .'">'.
				dms()->backend->render_box(
					$data['id_prefix'] . $id .'-'. $increment_placeholder .'-box',
					'&nbsp;',
					'<div class="dms-option-box-options dms-force-xs">'.
						dms()->backend->render_options($box_options, $values, array(
							'id_prefix'   => $data['id_prefix'] . $id .'-'. $increment_placeholder .'-',
							'name_prefix' => $data['name_prefix'] .'['. $id .']['. $increment_placeholder .']',
						)).
					'</div>',
					array(
						'html_after_title' => $controls_html,
					)
				).
			'</div>'
		);
	?>">
	<div class="dms-option-boxes-controls">
		<?php
		echo dms_html_tag('button', array(
			'type'    => 'button',
			'onclick' => 'return false;',
			'class'   => 'button dms-option-boxes-add-button',
			'data-increment' => ++$i,
			'data-increment-placeholder' => $increment_placeholder,
			'data-limit' => intval($option['limit']),
		), dms_htmlspecialchars($option['add-button-text']));
		?>
	</div>
</div>
