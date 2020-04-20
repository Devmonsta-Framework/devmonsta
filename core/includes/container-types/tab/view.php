<?php if (!defined('DMS')) die('Forbidden');
/**
 * @var array $tabs
 * @var array $values
 * @var array $options_data
 */

$global_lazy_tabs = dms()->theme->get_config('lazy_tabs');

?>
<div class="dms-options-tabs-wrapper">
	<div class="dms-options-tabs-list">
		<ul>
			<?php foreach ($tabs as $tab_id => &$tab): ?>
				<li <?php echo isset($tab['li-attr']) ? dms_attr_to_html($tab['li-attr']) : ''; ?> >
					<a href="#dms-options-tab-<?php echo esc_attr($tab_id) ?>" class="nav-tab dms-wp-link" ><?php
						echo htmlspecialchars($tab['title'], ENT_COMPAT, 'UTF-8') ?></a>
				</li>
			<?php endforeach; unset($tab); ?>
		</ul>
		<div class="dms-clear"></div>
	</div>
	<div class="dms-options-tabs-contents metabox-holder">
		<div class="dms-inner">
			<?php
			foreach ($tabs as $tab_id => &$tab):
				// prepare attributes
				{
					$attr = isset($tab['attr']) ? $tab['attr'] : array();

					$lazy_tabs = isset($tab['lazy_tabs']) ? $tab['lazy_tabs'] : $global_lazy_tabs;

					$attr['id'] = 'dms-options-tab-'. esc_attr($tab_id);

					if (!isset($attr['class'])) {
						$attr['class'] = 'dms-options-tab';
					} else {
						$attr['class'] = 'dms-options-tab '. $attr['class'];
					}

					if ($lazy_tabs) {
						$attr['data-dms-tab-html'] = dms()->backend->render_options(
							$tab['options'], $values, $options_data
						);
					}
				}
				?><div <?php echo dms_attr_to_html($attr) ?>><?php
					echo $lazy_tabs ? '' : dms()->backend->render_options($tab['options'], $values, $options_data);
				?></div><?php
				unset($tabs[$tab_id]); // free memory after printed and not needed anymore
			endforeach;
			unset($tab);
			?>
		</div>
	</div>
	<div class="dms-clear"></div>
</div>
