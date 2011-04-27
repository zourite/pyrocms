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
 * PyroCMS Files Attachments Controller
 *
 * Provides an admin for the attachments of files module.
 *
 * @author		Marcos Coelho <marcos@marcoscoelho.com>
 * @package		PyroCMS
 * @subpackage	Files
 */
class Admin_attachments extends Admin_Controller {

	public	$folders_tree		= array();
	private	$_folders			= array();
	private	$_validation_rules	= array(
		'default' => array(
			array(
				'field' => 'attachments_key',
				'label' => 'lang:files_attached.attachments_key_label',
				'rules' => 'trim|required|exact_length[40]'
			)
		),
		'file_browser' => array(
			array(
				'field' => 'attachment_value',
				'label' => 'lang:files_attached.attachment_value_label',
				'rules' => 'trim'
			),
		),
		'file_upload' => array(
			array(
				'field' => 'attachment_value',
				'label' => 'lang:files_attached.attachment_value_label',
				'rules' => 'trim'
			),
		),
		'link' => array(
			array(
				'field' => 'link_url',
				'label' => 'lang:files_attached.link_url_label',
				'rules' => 'trim|prep_url|required|max_length[255]|callback__check_link_url'
			),
			array(
				'field' => 'link_title',
				'label' => 'lang:files_attached.link_title_label',
				'rules' => 'trim|max_length[100]'
			),
			array(
				'field' => 'link_class',
				'label' => 'lang:files_attached.link_class_label',
				'rules' => 'trim|max_length[30]'
			)
		)
	);

	public function __construct()
	{
		parent::Admin_Controller();

		$this->load->models(array('file_m', 'file_folders_m', 'files_attached_m'));
		$this->lang->load('files');
		$this->lang->load('files_attached');
		$this->config->load('files');

		$this->_folders = $this->file_folders_m->get_folders();

		// Array for select
		$this->folders_tree = array();
		foreach ($this->_folders as $folder)
		{
			$this->folders_tree[$folder->id] = repeater('&raquo; ', $folder->depth) . $folder->name;
		}
	}
	
	public function attach($type = '')
	{
		if ( ! isset($this->_validation_rules[$type]))
		{
			if ($this->is_ajax())
			{
				$data['messages']['error'] = lang('files_attached.invalid_type_error');
				$message = $this->load->view('admin/partials/notices', $data, TRUE);

				return print( json_encode((object) array(
					'status'	=> 'error',
					'message'	=> $message
				)) );
			}
		}

		$rules = array_merge($this->_validation_rules['default'], $this->_validation_rules[$type]);

		$this->load->library('form_validation');
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run())
		{
			$input	= $this->input->post();
			$key	= $input['attachments_key'];
			$value	= $this->{'_get_' . $type . '_field'}($input, 'value');
			$title	= $this->{'_get_' . $type . '_field'}($input, 'title');
			$extra	= $this->{'_get_' . $type . '_field'}($input, 'extra');

			$data = array(
				'key'			=> $key,
				'type'			=> $type,
				'value'			=> $value,
				'title'			=> $title,
				'extra'			=> $extra,
				'created_on'	=> now(),
				'updated_on'	=> now()
			);

			if ($id = $this->files_attached_m->insert($data))
			{
				$attachment	= $this->files_attached_m->get($id);

				$status		= 'success';
				$message	= sprintf(lang('files_attached.create_success'), $attachment->title);
			}
			else
			{
				$status		= 'success';
				$message	= lang('files_attached.create_error');
			}

			if ($this->is_ajax())
			{
				$attachment_arr = isset($attachment) ? compact('attachment') : array();

				$data = array();
				$data['messages'][$status] = $message;
				$message = $this->load->view('admin/partials/notices', $data, TRUE);

				return print( json_encode((object) (array(
					'status'	=> $status,
					'message'	=> $message
				) + $attachment_arr)) );
			}
		}
		else if (validation_errors())
		{
			if ($this->is_ajax())
			{
				$message = $this->load->view('admin/partials/notices', array(), TRUE);

				return print( json_encode((object) array(
					'status'	=> 'error',
					'message'	=> $message
				)) );
			}
		}
	}

	function file_browser($action = '', $value = '')
	{
		switch ($action)
		{
			case 'contents';
				return $this->_get_folder_contents($value);
		}

		show_404();
	}

	function _get_folder_contents($id = 0)
	{
//		if ($this->is_ajax())
//		{
//			return print( json_encode((object) array(
//				'status'	=> 'error',
//				'message'	=> 'not found'
//			)) );
//		}
		
		$files = $this->file_m
			->order_by('date_added', 'DESC')
			->order_by('id', 'DESC')
			->get_many_by('folder_id', $id);

		foreach ($files as &$file)
		{
			$file = array(
				'id'	=> $file->id,
				'name'	=> $file->name,
				'type'	=> $file->type,
				'source'=> site_url('uploads/files/' . $file->filename),
				'thumb'	=> $file->type === 'i' ? site_url('files/thumb/' . $file->id . '/64/64') : image_url($file->type . '.png', 'files'),
			);
		}

		return print( json_encode((object) array(
			'status'	=> 'success',
			'files'		=> $files
		)) );
	}

	public function _get_file_browser_field($data = array(), $name = '')
	{
		switch ($name)
		{
			case 'value';
				return md5($data['file_id']);

			case 'title';
				$file = $this->file_m->get($data['file_id']);
				return $file->name;

			case 'extra';
				unset($data['attachments_key']);
				$arr = array();
				foreach ($data as $field => $value)
				{
					$arr[substr($field, 13)] = $value;
				}
				return (object) $arr;
		}

		return NULL;
	}

	public function _get_file_upload_field($data = array(), $name = '')
	{
		switch ($name)
		{
			case 'value';
				return md5($data['file_id']);

			case 'title';
				$file = $this->file_m->get($data['file_id']);
				return $file->name;

			case 'extra';
				unset($data['attachments_key']);
				$arr = array();
				foreach ($data as $field => $value)
				{
					$arr[substr($field, 12)] = $value;
				}
				return (object) $arr;
		}

		return NULL;
	}

	public function _get_link_field($data = array(), $name = '')
	{
		switch ($name)
		{
			case 'value';
				return md5($data['link_url']);

			case 'title';
				return $data['link_url'];

			case 'extra';
				unset($data['attachments_key']);
				$arr = array();
				foreach ($data as $field => $value)
				{
					$arr[substr($field, 5)] = $value;
				}
				return (object) $arr;
		}

		return NULL;
	}

	public function _check_key($key = '')
	{
		// TODO: Valid key

		return TRUE;
	}

	public function _check_link_url($url = '')
	{
		if ($this->files_attached_m->count_by(array(
			'key'	=> $this->input->post('attachments_key'),
			'value'	=> md5($url),
		)) > 0)
		{
			$this->form_validation->set_message('_check_link_url', sprintf(lang('files_attached.already_exists_error'), $url));
			return FALSE;
		}

		return $url;
	}
}