<?php

namespace Devmonsta\Options\Posts;

use Devmonsta\Traits\Singleton;

class View
{

	use Singleton;

	protected $meta_owner = "post";



	/**
	 * Build the metbox for the post
	 *
	 * @access      public
	 * @return      void
	 */

	public function build($box_id, $controls)
	{


		echo '<div class="devm-box">'; // This html for wrapper purpose

		foreach ($controls as $control) {

			if ($control['box_id'] == $box_id) {
				$this->render($control);
			}
		}

		echo '</div>';
	}

	/**
	 * Render markup view for the control
	 * defined in theme. It will pass the data according to the
	 * control type
	 *
	 * @access      public
	 * @return      void
	 */
	public function render($control_content)
	{


		if (isset($control_content['type'])) {

			if ($control_content['type'] == 'repeater') {

				$this->build_repeater($control_content);
			}

			$this->build_controls($control_content);
		}
	}

	/**
	 * Build controls markup
	 *
	 * @access  public
	 * @return  void
	 */

	public function build_controls($control_content)
	{
		$class_name = explode('-', $control_content['type']);
		$class_name = array_map('ucfirst', $class_name);
		$class_name = implode('', $class_name);
		$control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

		if (class_exists($control_class)) {

			$control = new $control_class($control_content);
			$control->init();
			$control->enqueue($this->meta_owner);
			$control->render();

		} else {

			$file = plugin_dir_path(__FILE__) . 'controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';

			if (file_exists($file)) {

				include_once $file;

				if (class_exists($control_class)) {

					$control = new $control_class($control_content);
					$control->init();
					$control->enqueue($this->meta_owner);
					$control->render();
				}
			}
		}
	}

	/**
	 * Build repeater controls
	 *
	 * @access  public
	 * @return  void
	 */

	public function build_repeater($control_data)
	{
		/**
		 * Incomplete code , just testing , do not read or use
		 */
		if (isset($control_data['controls'])) {

			?>
			<div id="<?php echo esc_attr( $control_data['name'] ); ?>" class="devm-option form-field ">

				<div class='devm-option-column left'>
					<label class="devm-option-label"><?php echo esc_html( $control_data['label'] ); ?> </label>
				</div>


				<div class='devm-option-column devm-repeater-column right'>

					<div class="devm-repeater-control devm-repeater-sample">
						<a href="#" data-id="<?php echo esc_attr( $control_data['name'] ); ?>" class="devm-repeater-control-action">Control
							<button type="button" data-id="<?php echo esc_attr( $control_data['name'] ); ?>"
							        class="components-button devm-editor-post-trash is-link"><span
									class="dashicons dashicons-dismiss"></span></button>
						</a>

						<div class="devm-repeater-inner-controls" id="<?php echo esc_attr( $control_data['name'] ); ?>">
							<div class="devm-repeater-inner-controls-inner">
								<div class="devm-repeater-popup-heading">
									<span class="devm-repeater-popup-close dashicons dashicons-no-alt"></span>
								</div>
								<div class="devm-repeater-popup-data">
									<?php
									ob_start();
									$this->repeater_controls($control_data);
									$output = ob_get_clean();

									echo str_replace("active-script", '', $output);
									?>
								</div>
								<div class="devm-repeater-popup-footer">
								</div>
							</div>
						</div>
					</div>

					<div id="devm-repeater-control-list-<?php echo esc_attr( $control_data['name'] ); ?>"
					     class="devm-repeater-control-list">

					</div>


					<a href='' data-id='<?php echo esc_attr( $control_data['name'] ); ?>'
					   class='devm-repeater-add-new button'><?php echo esc_html( $control_data['add_new'] ); ?></a>
				</div>
			</div>

			<?php
		}
	}

	public function repeater_controls($control_data)
	{
		foreach ($control_data['controls'] as $control_content) {

			if ($control_content['type'] == 'repeater')
				$this->repeater_control_markup($control_content);

			$name = $control_content['name'];
			unset($control_content['name']);
			$control_content['name'] = $name;
			$class_name = explode('-', $control_content['type']);
			$class_name = array_map('ucfirst', $class_name);
			$class_name = implode('', $class_name);
			$control_class = 'Devmonsta\Options\Posts\Controls\\' . $class_name . '\\' . $class_name;

			if (class_exists($control_class)) {

				$control = new $control_class($control_content);
				$control->init();
				$control->render();

			} else {

				$file = plugin_dir_path(__FILE__) . 'controls/' . $control_content['type'] . '/' . $control_content['type'] . '.php';

				if (file_exists($file)) {

					include_once $file;

					if (class_exists($control_class)) {

						$control = new $control_class($control_content);
						$control->init();
						$control->render();
					}
				}
			}
		}
	}


	public function repeater_control_markup($control_content)
	{
		?>
		<div class='devm-option form-field devm-repeater-child'>
			<div class='devm-option-column left'>
				<label class='devm-option-label'><?php echo esc_html( $control_content['lable'] ); ?> </label>
			</div>
			<div class='devm-option-column right'>

				<div class='devm-option-column devm-repeater-column right'>

					<div class="devm-repeater-control devm-repeater-sample">
						<a href="#" data-id="<?php echo esc_attr( $control_content['name'] ); ?>"
						   class="devm-repeater-control-action">Control

							<button type="button" data-id="<?php echo esc_attr( $control_content['name'] ); ?>"
							        class="components-button devm-editor-post-trash is-link">
								<span class="dashicons dashicons-dismiss"></span>
							</button>

						</a>

						<div class="devm-repeater-inner-controls" id="<?php echo esc_attr( $control_content['name'] ); ?>">

							<div class="devm-repeater-inner-controls-inner">

								<div class="devm-repeater-popup-heading">
									<span class="devm-repeater-popup-close dashicons dashicons-no-alt"></span>
								</div>

								<div class="devm-repeater-popup-data">
									<?php
									ob_start();
									$this->sub_repeater_controls($control_content['controls']);
									$output = ob_get_clean();

									echo str_replace("active-script", '', $output);
									?>
								</div>

								<div class="devm-repeater-popup-footer"></div>

							</div>
						</div>
					</div>

					<div class="devm-repeater-control-list"></div>

					<a href='' data-id='<?php echo esc_attr( $control_content['name'] ); ?>' class='devm-repeater-add-new button'>Add
						New</a>
				</div>
			</div>
		</div>

		<?php

	}

	public function sub_repeater_controls($control_contents)
	{
		foreach ($control_contents as $control_content) {

			$this->build_controls($control_content);

		}

	}
}