<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Restricts access based on ip address whitelist in config.
 *
 * @license		http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author		Mike Funk
 * @link		http://mikefunk.com
 * @email		mike@mikefunk.com
 *
 * @file		ip_restrict.php
 * @version		1.1.1
 * @date		8/16/12
 */

class ip_restrict
{
	// --------------------------------------------------------------------------
	
	/**
	 * codeigniter superobject
	 * 
	 * @var object
	 * @access private
	 */
	private $_ci;
	
	// --------------------------------------------------------------------------
	
	/**
	 * load resources.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->_ci =& get_instance();
		$this->_ci->config->load('ip_restrict');
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * restrict by whitelist or blacklist, redirect or display error message on
	 * fail.
	 * 
	 * @access public
	 * @return bool if it succeeds, return true
	 */
	public function restrict()
	{
		$cont = false;
		$ip = $this->_ci->input->ip_address();
		
		// whitelist restriction
		if (config_item('ip_restrict_mode') == 'whitelist')
		{
			$whitelist = config_item('ip_restrict_whitelist');
			foreach ($whitelist as $item)
			{
				if ($ip == $item)
				{
					$cont = true;
				}
			}
			
			if (!$cont)
			{
				log_message('error', 'ip address ' . $ip . ' not found in whitelist.');
			}
		}
		// blacklist restriction
		elseif (config_item('ip_restrict_mode') == 'blacklist')
		{
			$cont = true;
			$blacklist = config_item('ip_restrict_blacklist');
			foreach ($blacklist as $item)
			{
				if ($ip == $item)
				{
					$cont = false;
				}
			}
			
			if (!$cont)
			{
				log_message('error', 'ip address ' . $ip . ' found in blacklist.');
			}
		}
		
		// if access is restricted
		if (!$cont)
		{
			// show error if configured
			if (config_item('ip_restrict_action') == 'display_error')
			{
				echo config_item('ip_restrict_error_message');
				exit();
			}
			// redirect if configured
			elseif (config_item('ip_restrict_action') == 'redirect')
			{
				$this->_ci->load->helper('url');
				redirect(config_item('ip_restrict_redirect_url'));
			}
		}
		else
		{
			return true;
		}
	}
	
	// --------------------------------------------------------------------------
}
/* End of file ip_restrict.php */
/* Location: ip_restrict/libraries/ip_restrict.php */