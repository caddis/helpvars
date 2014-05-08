<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helpvars Extension
 *
 * @package Helpvars
 * @author  Caddis
 * @link    http://www.caddis.co
 */

class Helpvars_ext {

	public $name = 'Helpvars';
	public $version = '1.3.0';
	public $description = 'Make various segment and helper variables available globally.';
	public $docs_url = 'https://github.com/caddis/helpvars';
	public $settings_exist = 'n';

	/**
	 * Activate Extension
	 * 
	 * @return void
	 */
	public function activate_extension()
	{
		ee()->db->insert('extensions', array(
			'class' => __CLASS__,
			'method' => 'template_fetch_template',
			'hook' => 'template_fetch_template',
			'settings' => '',
			'priority' => 10,
			'version' => $this->version,
			'enabled' => 'y'
		));
	}

	/**
	 * Update Extension
	 *
	 * @return mixed void on update / false if none
	 */
	public function update_extension($current = '')
	{
		if ($current == $this->version) {
			return false;
		}

		return true;
	}

	/**
	 * Disable Extension
	 *
	 * @return void
	 */
	public function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	/**
	 * Method for template_fetch_template hook
	 * Based on low seg2cat
	 *
	 * @return void
	 */
	public function template_fetch_template()
	{
		$data = array();

		// General variables
		$data['all_segments'] = implode('/', ee()->uri->segments);
		$data['is_ajax'] = ee()->input->is_ajax_request();
		$data['is_https'] = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? true : false;

		// Member variables
		$data['can_access_cp'] = ee()->session->userdata('can_access_cp');

		if (REQ == 'PAGE' and ! empty(ee()->uri->segments)) {
			$cats = $segs = $groups = array();
			$site = ee()->config->item('site_id');

			$data['segment_category_ids'] = '';

			$segments = ee()->uri->segments;

			if (preg_match('/^[P][0-9]+$/i', end($segments))) {
				array_pop($segments);
			}

			// Loop through segments and set default data
			foreach ($segments as $num => $seg) {
				$data['segment_' . $num . '_category_id'] = '';
				$data['segment_' . $num . '_category_name'] = '';
				$data['segment_' . $num . '_category_description'] = '';
				$data['segment_' . $num . '_category_image'] = '';
				$data['segment_' . $num . '_category_parent_id'] = '';

				$segs[] = $seg;
			}

			// Grab category database results

			$query = ee()->db->select('cat_id, cat_url_title, cat_name, cat_description, cat_image, group_id, parent_id')
				->from('exp_categories')
				->where('site_id', $site)
				->where_in('cat_url_title', $segs)
				->get();

			// If we have matching categories, continue...
			if ($query->num_rows() > 0) {
				// Flip segment array to get 'segment_1' => '1'
				$ids = array_flip($segments);

				// Loop through categories
				foreach ($query->result_array() as $row) {
					// Override values in data array
					$data['segment_' . $ids[$row['cat_url_title']] . '_category_id'] = $row['cat_id'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_category_name'] = $row['cat_name'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_category_description'] = $row['cat_description'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_category_image'] = $row['cat_image'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_category_parent_id'] = $row['parent_id'];

					$data['segment_' . $ids[$row['cat_url_title']] . '_group_' . $row['group_id'] . '_category_id'] = $row['cat_id'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_group_' . $row['group_id'] . '_category_name'] = $row['cat_name'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_group_' . $row['group_id'] . '_category_description'] = $row['cat_description'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_group_' . $row['group_id'] . '_category_image'] = $row['cat_image'];
					$data['segment_' . $ids[$row['cat_url_title']] . '_group_' . $row['group_id'] . '_category_parent_id'] = $row['parent_id'];

					$cats[] = $row['cat_id'];
					$groups[$row['group_id']][] = $row['cat_id'];

					if ($ids[$row['cat_url_title']] === count($ids)) {
						$data['last_segment_category_id'] = $row['cat_id'];
						$data['last_segment_category_name'] = $row['cat_name'];
						$data['last_segment_category_description'] = $row['cat_description'];
						$data['last_segment_category_image'] = $row['cat_image'];
						$data['last_segment_category_parent_id'] = $row['parent_id'];

						$data['last_segment_group_' . $row['group_id'] . '_category_id'] = $row['cat_id'];
						$data['last_segment_group_' . $row['group_id'] . '_category_name'] = $row['cat_name'];
						$data['last_segment_group_' . $row['group_id'] . '_category_description'] = $row['cat_description'];
						$data['last_segment_group_' . $row['group_id'] . '_category_image'] = $row['cat_image'];
						$data['last_segment_group_' . $row['group_id'] . '_category_parent_id'] = $row['parent_id'];
					}
				}

				// Create stack of all segment category ids
				$data['segment_category_ids'] = implode('&', $cats);
				$data['segment_category_ids_any'] = implode('|', $cats);

				foreach ($groups as $key => $val) {
					$data['segment_group_' . $key . '_category_ids'] = implode('&', $val);
					$data['segment_group_' . $key . '_category_ids_any'] = implode('|', $val);
				}
			}
		}

		ee()->config->_global_vars = ee()->config->_global_vars + $data;
	}
}