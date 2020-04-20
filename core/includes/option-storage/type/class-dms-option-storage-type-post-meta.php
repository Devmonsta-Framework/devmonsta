<?php if (!defined('DMS')) die('Forbidden');

/**
 * array(
 *  'post-id' => 3 // optional // hardcoded post id
 *  'post-meta' => 'hello_world' // optional (default: 'dms:opt:{option_id}')
 *  'key' => 'option_id/sub_key' // optional @since 2.5.3
 * )
 */
class DMS_Option_Storage_Type_Post_Meta extends DMS_Option_Storage_Type {
	public function get_type() {
		return 'post-meta';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _save( $id, array $option, $value, array $params ) {
		if ($post_id = $this->get_post_id($option, $params)) {
			$meta_id = $this->get_meta_id($id, $option, $params);

			if (isset($option['dms-storage']['key'])) {
				$meta_value = get_post_meta($post_id, $meta_id, true);

				dms_aks($option['dms-storage']['key'], $value, $meta_value);

				dms_update_post_meta($post_id, $meta_id, $meta_value);

				unset($meta_value);
			} else {
				dms_update_post_meta($post_id, $meta_id, $value);
			}

			return dms()->backend->option_type($option['type'])->get_value_from_input(
				array('type' => $option['type']), null
			);
		} else {
			return $value;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _load( $id, array $option, $value, array $params ) {
		if ($post_id = $this->get_post_id($option, $params)) {
			$meta_id = $this->get_meta_id($id, $option, $params);
			$meta_value = get_post_meta($post_id, $meta_id,
				/**
				 * Do not set this to `true` because the below verification if value exists or not will be impossible
				 * because if the value is not in db it will be an empty string '' instead of NULL
				 * so we can't treat empty string as non value because the actual value can be an empty string
				 */
				false
			);

			if (empty($meta_value)) {
				return $value;
			} else {
				$meta_value = $meta_value[0];
			}

			if (isset($option['dms-storage']['key'])) {
				return dms_akg($option['dms-storage']['key'], $meta_value, $value);
			} else {
				return $meta_value;
			}
		} else {
			return $value;
		}
	}

	private function get_post_id($option, $params) {
		$post_id = null;

		if (!empty($option['dms-storage']['post-id'])) {
			$post_id = $option['dms-storage']['post-id'];
		} elseif (!empty($params['post-id'])) {
			$post_id = $params['post-id'];
		}

		$post_id = intval($post_id);

		if ($post_id > 0) {
			return $post_id;
		} else {
			return false;
		}
	}

	private function get_meta_id($id, $option, $params) {
		return empty($option['dms-storage']['post-meta'])
			? 'dms:opt:'. $id
			: $option['dms-storage']['post-meta'];
	}
}
