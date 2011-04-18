<?php  defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * PyroCMS
 *
 * An open source CMS based on CodeIgniter
 *
 * @package		PyroCMS
 * @author		PyroCMS Dev Team
 * @license		Apache License v2.0
 * @link		http://pyrocms.com
 * @since		Version 1.1.0
 * @filesource
 */

/**
 * PyroCMS File Attached Model
 *
 * Integration of the attachments with any module to create links for download of
 * files in website frontend, create thumbnails or feature image for pages, etc...
 *
 * @author		Marcos Coelho <marcos@marcoscoelho.com>
 * @package		PyroCMS
 * @subpackage	Files
 */
class Files_attached_m extends MY_Model {

	protected $_table = 'files_attached';

	public function generate_key()
	{
		return sha1(uniqid());
	}

	public function get($id = 0)
	{
		$result = parent::get($id);

		if ($result)
		{
			$result = $this->unserialize($result);
		}

		return $result;
	}

	public function get_by($input = array())
	{
		$result = parent::get_by($input);

		if ($result)
		{
			$result = $this->unserialize($result);
		}

		return $result;
	}

	public function get_all()
	{
		$result = parent::get_all();

		if ($result)
		{
			$result = array_map(array($this, 'unserialize'), $result);
		}

		return $result;
	}

	public function insert($input = array())
	{
		foreach (array('key','type','value') as $required_field)
		{
			if ( ! isset($input[$required_field]))
			{
				return FALSE;
			}
		}

		foreach (array('created', 'updated') as $date_field)
		{
			$date_field = $date_field . '_on';

			if ( ! (isset($input[$date_field]) && $input[$date_field]))
			{
				$input[$date_field] = now();
			}
		}

		if ( ! isset($input['order']))
		{
			// Get the last position of order count
			$last_attachment = $this->db
				->select('`order`')
				->order_by('`order`', 'desc')
				->limit(1)
				->get_where($this->_table, array('key' => $input['key']))
				->row();

			$input['order'] = isset($last_attachment->order) ? $last_attachment->order + 1 : 1;
		}

		if (isset($input['extra']))
		{
			$input['extra'] = json_encode($input['extra']);
		}

		return parent::insert($input);
	}

	public function unserialize($obj)
	{
		if (isset($obj->extra))
		{
			$obj->extra = json_decode($obj->extra);
		}

		return $obj;
	}
}

/* End of file files_attached_m.php */
/* Location: ./system/pyrocms/modules/files/models/files_attached_m.php */ 