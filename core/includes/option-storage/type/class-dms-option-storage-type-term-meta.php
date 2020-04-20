<?php if (!defined('DMS')) die('Forbidden');

/**
 * array(
 *  'term-id' => 3 // optional // hardcoded term id
 *  'term-meta' => 'hello_world' // optional (default: 'dms:opt:{option_id}')
 *  'key' => 'option_id/sub_key' // optional
 * )
 */
class DMS_Option_Storage_Type_Term_Meta extends DMS_Option_Storage_Type {
	public function get_type() {
		return 'term-meta';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _save( $id, array $option, $value, array $params ) {
		if ($term_id = $this->get_term_id($option, $params)) {
			$meta_id = $this->get_meta_id($id, $option, $params);

			if (isset($option['dms-storage']['key'])) {
				$meta_value = get_term_meta($term_id, $meta_id, true);

				dms_aks($option['dms-storage']['key'], $value, $meta_value);

				update_term_meta($term_id, $meta_id, $meta_value);

				unset($meta_value);
			} else {
				update_term_meta($term_id, $meta_id, $value);
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
		if ($term_id = $this->get_term_id($option, $params)) {
			$meta_id = $this->get_meta_id($id, $option, $params);
			$meta_value = get_term_meta($term_id, $meta_id, true);

			if ($meta_value === '' && is_array($value)) {
				return $value;
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

	private function get_term_id($option, $params) {
		$term_id = null;

		if (!empty($option['dms-storage']['term-id'])) {
			$term_id = $option['dms-storage']['term-id'];
		} elseif (!empty($params['term-id'])) {
			$term_id = $params['term-id'];
		}

		$term_id = intval($term_id);

		if ($term_id > 0) {
			return $term_id;
		} else {
			return false;
		}
	}

	private function get_meta_id($id, $option, $params) {
		return empty($option['dms-storage']['term-meta'])
			? 'dms:opt:'. $id
			: $option['dms-storage']['term-meta'];
	}
}
