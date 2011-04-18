<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_page_attachments extends Migration {

	function up()
	{
		$this->dbforge->add_column('pages', array(
			'attachments_key' => array(
				'type'			=> 'varchar',
				'constraint'	=> 40,
				'null'			=> FALSE,
				'collation'		=> 'utf8_unicode_ci'
			)
		));

		$this->load->model('files/files_attached_m');

		$ids = $this->db->select('id')->get('pages')->result();
		foreach ($ids as $id)
		{
			$this->db
				->where('id', $page->id)
				->update('pages', array(
					'attachments_key' => $this->files_attached_m->generate_key()
				));
		}

		$this->pyrocache->delete_all('pages_m');
	}

	function down()
	{
		$this->dbforge->drop_column('pages', 'attachments_key');
		$this->pyrocache->delete_all('pages_m');
	}
}