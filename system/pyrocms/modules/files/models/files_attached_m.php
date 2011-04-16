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
 * @since		Version 1.0-dev
 * @filesource
 */

/**
 * PyroCMS File Attachments Model
 *
 * Service for attachments
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

/* End of file file_folders_m.php */
/* Location: ./system/pyrocms/modules/files/models/file_folders_m.php */ 