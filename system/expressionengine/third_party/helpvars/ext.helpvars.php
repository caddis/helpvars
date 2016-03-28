<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Helpvars Extension
*
* @package Helpvars
* @author  Caddis
* @link    http://www.caddis.co
*/

include_once(PATH_THIRD . 'helpvars/addon.setup.php');

class Helpvars_ext {

	public $name = HELPVARS_NAME;
	public $version = HELPVARS_VER;
	public $description = HELPVARS_DESC;
	public $docs_url = HELPVARS_DOCS_URL;
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
	 * @param string $current
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
		static $set;

		if ($set !== true) {
			$data = array();
			$segments = ee()->uri->segments;

			// General variables
			$data['all_segments'] = implode('/', $segments);
			$data['is_ajax'] = ee()->input->is_ajax_request();
			$data['is_pjax'] = ee()->input->get_request_header('X-pjax') !== false;
			$data['is_https'] = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? true : false;

			// Member variables
			$data['can_access_cp'] = ee()->session->userdata('can_access_cp');
			$data['logged_in_member_id'] = ee()->session->userdata('member_id');
			$data['logged_in_group_id'] = ee()->session->userdata('group_id');

			$data['paginated'] = false;
			$data['not_paginated'] = true;

			if (preg_match('/^[P][0-9]+$/i', end($segments))) {
				array_pop($segments);

				$data['paginated'] = true;
				$data['not_paginated'] = false;
			}

			// All segments except pagination
			$data['base_segments'] = implode('/', $segments);

			$segments = array_map('strtolower', $segments);

			$seg_count = count($segments);

			if (ee()->config->item('helpvars_set_category_vars') !== 'n') {
				$data['last_segment_primary'] = '';
				$data['last_segment_category_id'] = '';
				$data['last_segment_category_name'] = '';
				$data['last_segment_category_description'] = '';
				$data['last_segment_category_image'] = '';
				$data['last_segment_category_parent_id'] = '';

				if (REQ == 'PAGE' and $seg_count > 0) {
					$cats = $segs = $groups = array();
					$site = ee()->config->item('site_id');

					$data['segment_category_ids'] = '';
					$data['last_segment_primary'] = end($segments);

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
					$query = ee()->db
						->select('cat_id, cat_url_title, cat_name, cat_description, cat_image, group_id, parent_id')
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
							$seg = $ids[$row['cat_url_title']];
							$group_seg = $seg . '_group_' . $row['group_id'];

							// Override values in data array
							$data['segment_' . $seg . '_category_id'] = $row['cat_id'];
							$data['segment_' . $seg . '_category_name'] = $row['cat_name'];
							$data['segment_' . $seg . '_category_description'] = $row['cat_description'];
							$data['segment_' . $seg . '_category_image'] = $row['cat_image'];
							$data['segment_' . $seg . '_category_parent_id'] = $row['parent_id'];

							$data['segment_' . $group_seg . '_category_id'] = $row['cat_id'];
							$data['segment_' . $group_seg . '_category_name'] = $row['cat_name'];
							$data['segment_' . $group_seg . '_category_description'] = $row['cat_description'];
							$data['segment_' . $group_seg . '_category_image'] = $row['cat_image'];
							$data['segment_' . $group_seg . '_category_parent_id'] = $row['parent_id'];

							$cats[] = $row['cat_id'];
							$groups[$row['group_id']][] = $row['cat_id'];

							if ($ids[$row['cat_url_title']] === count($ids)) {
								$group_id = $row['group_id'];

								$data['last_segment_category_id'] = $row['cat_id'];
								$data['last_segment_category_name'] = $row['cat_name'];
								$data['last_segment_category_description'] = $row['cat_description'];
								$data['last_segment_category_image'] = $row['cat_image'];
								$data['last_segment_category_parent_id'] = $row['parent_id'];

								$data['last_segment_group_' . $group_id . '_category_id'] = $row['cat_id'];
								$data['last_segment_group_' . $group_id . '_category_name'] = $row['cat_name'];
								$data['last_segment_group_' . $group_id . '_category_description'] = $row['cat_description'];
								$data['last_segment_group_' . $group_id . '_category_image'] = $row['cat_image'];
								$data['last_segment_group_' . $group_id . '_category_parent_id'] = $row['parent_id'];
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

				// Loop through additional segments
				$max_empty_segments = ee()->config->item('helpvars_max_empty_segments');

				if (is_int($max_empty_segments) and $max_empty_segments > $seg_count) {
					for ($i = $seg_count + 1; $i <= $max_empty_segments; $i++) {
						$data['segment_' . $i . '_category_id'] = '';
						$data['segment_' . $i . '_category_name'] = '';
						$data['segment_' . $i . '_category_description'] = '';
						$data['segment_' . $i . '_category_image'] = '';
						$data['segment_' . $i . '_category_parent_id'] = '';
					}
				}
			}

			$set = true;

			ee()->config->_global_vars = ee()->config->_global_vars + $data;
		}
	}
}
