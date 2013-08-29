<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package  Helpvars
 * @author   Michael Leigeber
 * @license  http://www.apache.org/licenses/LICENSE-2.0
 * @link     http://www.caddis.co
 */

class Helpvars_ext {

	public $EE;
	public $name = 'HelpVars';
	public $version = '1.0.0';
	public $description = 'Make various helper variables available globally';
	public $docs_url = '';
	public $settings_exist = 'n';
	public $config;

	/**
	 * Constructor
	 *
	 * @param  mixed Settings array or empty string if none exist
	 * @return void
	 */
	public function __construct($settings = array())
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}

	/**
	 * Activate Extension
	 * 
	 * @return void
	 */
	public function activate_extension()
	{
		$this->EE->db->insert('extensions',
			array(
				'class' => __CLASS__,
				'method' => 'template_fetch_template',
				'hook' => 'template_fetch_template',
				'settings' => '',
				'priority' => 10,
				'version' => $this->version,
				'enabled' => 'y'
			)
		);
	}

	/**
	 * Disable Extension
	 *
	 * @return void
	 */
	public function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}
	
	/**
	 * Method for template_fetch_template hook
	 *
	 * @return void
	 */
	public function template_fetch_template()
	{
		$data = array();

		// General variables
		$data['all_segments'] = implode('/', $this->EE->uri->segments);
		$data['is_ajax'] = $this->EE->input->is_ajax_request();
		$data['is_https'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
		
		// Member variables
		$data['can_access_cp'] = $this->EE->session->userdata('can_access_cp');

		// Merge into global array
		$this->EE->config->_global_vars = array_merge($this->EE->config->_global_vars, $data);
		
		// Segment variables
		$this->set_category_segments();
	}
	
	/**
	 * This function is courtesy of Low Seg2Cat extension
	 *
	 * @return void
	 */
	private function set_category_segments()
	{
		// Only continue if request is a page and we have segments to check
		if (REQ != 'PAGE' || empty($this->EE->uri->segments))
		{
			return;
		}

		// Initiate variables
		$site = $this->EE->config->item('site_id');
		$data = $cats = $segs = array();
		$data['segment_category_ids'] = '';
		
		// loop through segments and set data array thus: segment_1_category_id etc
		foreach ($this->EE->uri->segments AS $nr => $seg)
		{
			$data['segment_' . $nr . '_category_id'] = '';
			$data['segment_' . $nr . '_category_name'] = '';
			$data['segment_' . $nr . '_category_description'] = '';
			$data['segment_' . $nr . '_category_image'] = '';
			$data['segment_' . $nr . '_category_parent_id'] = '';

			$segs[] = $seg;
		}

		// Compose query, get results
		$this->EE->db->select('cat_id, cat_url_title, cat_name, cat_description, cat_image, parent_id');
		$this->EE->db->from('exp_categories');
		$this->EE->db->where('site_id', $site);
		$this->EE->db->where_in('cat_url_title', $segs);

		$query = $this->EE->db->get();

		// If we have matching categories, continue...
		if ($query->num_rows())
		{
			// Load typography
			$this->EE->load->library('typography');

			// Flip segment array to get 'segment_1' => '1'
			$ids = array_flip($this->EE->uri->segments);
			
			// loop through categories
			foreach ($query->result_array() as $row)
			{
				// Overwrite values in data array
				$data['segment_' . $ids[$row['cat_url_title']] . '_category_id'] = $row['cat_id'];
				$data['segment_' . $ids[$row['cat_url_title']] . '_category_name'] = $row['cat_name'];
				$data['segment_' . $ids[$row['cat_url_title']] . '_category_description'] = $row['cat_description'];
				$data['segment_' . $ids[$row['cat_url_title']] . '_category_image'] = $row['cat_image'];
				$data['segment_' . $ids[$row['cat_url_title']] . '_category_parent_id'] = $row['parent_id'];
				
				$cats[] = $row['cat_id'];
				
				if ($ids[$row['cat_url_title']] == count($ids))
				{
					$data['last_segment_category_id'] = $row['cat_id'];
					$data['last_segment_category_name'] = $this->EE->typography->format_characters($row['cat_name']);
					$data['last_segment_category_description'] = $row['cat_description'];
					$data['last_segment_category_image'] = $row['cat_image'];
				}
			}
			
			// Create inclusive stack of all category ids present in segments
			$data['segment_category_ids'] = implode('&',$cats);
			$data['segment_category_ids_any'] = implode('|',$cats);
		}
		
		// Add data to global vars
		$this->EE->config->_global_vars = array_merge($this->EE->config->_global_vars, $data);
	}
}