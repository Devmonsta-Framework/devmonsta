<?php

namespace Devmonsta\Options\Customizer\Controls\Tswitch;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Switch control (modified checkbox).
 */
class Tswitch extends \WP_Customize_Control
{

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'tswitch';

	public function enqueue()
	{
		wp_enqueue_style('element-ui', 'https://unpkg.com/element-ui/lib/theme-chalk/index.css', [], null, '');
		wp_enqueue_script('vue', '//unpkg.com/vue/dist/vue.js', [], null);
		wp_enqueue_script('elment-script', "https://unpkg.com/element-ui/lib/index.js", [], null);
	}




	public function render_content()
	{
	?>


		<input <?php $this->link(); ?> type="text" name="tswitch">
		<h3>Value : <?php echo $this->value(); ?> </h3>


		<div id="app">
			<template>
				<el-select v-model="value" placeholder="Select">
					<el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
					</el-option>
				</el-select>
			</template>
		</div>

		<script src='<?php echo plugin_dir_url(__FILE__); ?>js/script.js'></script>
	<?php
	}
}
