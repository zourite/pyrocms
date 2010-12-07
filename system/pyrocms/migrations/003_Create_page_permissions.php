<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_page_permissions extends Migration {
	
	function up() 
	{
		$this->migrations->verbose AND print "Added page pemrissions...";

		// Setup Keys
		$this->db->query('CREATE TABLE IF NOT EXISTS `page_permissions` (
		  `id` int(11) NOT NULL auto_increment,
		  `page_id` int(11) NOT NULL,
		  `group_id` int(11) NOT NULL,
		  `access` tinyint(1) NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
	}

	function down() 
	{
		$this->db->query('DROP TABLE page_permissions');
	}
}
