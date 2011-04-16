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

	public function generate_key()
	{
		return sha1(uniqid());
	}
}

/* End of file files_attached_m.php */
/* Location: ./system/pyrocms/modules/files/models/files_attached_m.php */ 