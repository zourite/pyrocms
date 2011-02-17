<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Email Template Events Class
 * 
 * @package		PyroCMS
 * @subpackage	Email Templates
 * @category	events
 * @author		Stephen Cozart - PyroCMS Dev Team
 */
class Events_Widgets {
    
    protected $ci;
    
    protected $fallbacks = array();
    
    public function __construct()
    {
        $this->ci =& get_instance();
        
        //register the event
        Events::register('load_assets', array($this, 'load_assets'));
    }
    
    public function load_assets($data)
    {
        foreach($data as $id)
		{
			$this->_find_asset_by($id);
		}
    }
	
	public function _find_asset_by($id)
	{
		$this->ci->load->library('widgets/widgets');
		$widget = $this->ci->widgets->get_instance($id);
		
		$path = $this->ci->widgets->get_path($widget->slug);
		
		$asset_css = $path . 'css/widget_' . $widget->slug . '.css';
		$asset_js = $path . 'js/widget_' . $widget->slug . '.js';
		
		if(file_exists($asset_css))
		{
			$this->ci->template->append_metadata('<link href="'.$asset_css.'" rel="stylesheet" type="text/css" />' );
		}
		
		if(file_exists($asset_js))
		{
			$this->ci->template->append_metadata('<script type="text/javascript" src="'.$asset_js.'"></script>');
		}
	}
    
}
/* End of file events.php */