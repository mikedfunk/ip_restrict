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
 * @version		1.1.2
 * @date		9/6/12
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
        $ip = $this->_ci->input->ip_address();

        //get the mode (whitelist or blacklist)
        $mode = config_item('ip_restrict_mode');

        //get the list of IP addresses and ranges
        $list = config_item('ip_restrict_' . $mode);

        $match = false;
        foreach ($list as $item) {
            
            if(strpos($item, '/') !== false || strpos($item, '-') !== false || strpos($item, '*') !== false) {
                //the IP in the list is in one of the acceptable range formats
                $match = $this->ip_in_range($ip, $item);
            } elseif ($ip == $item) {
                $match = true;
            }

            //break as soon as a match is found
            if($match) break;
        }
        
        //if not a match and whitelist, or a match and blacklist, process the error
        if((!$match && $mode == 'whitelist') || ($match && $mode == 'blacklist')){
            log_message('error', 'ip address ' . $ip . ($match ? ' found' : ' not found') . ' in ' . $mode . '.');
            // show error if configured
            if (config_item('ip_restrict_action') == 'display_error') {
                echo config_item('ip_restrict_error_message');
                exit();
            }
            // redirect if configured
            elseif (config_item('ip_restrict_action') == 'redirect') {
                $this->_ci->load->helper('url');
                redirect(config_item('ip_restrict_redirect_url'));
            }
        } else {
            //ip address is allowed
            return true;
        }
	}

    protected function ip_in_range($ip, $range)
    {
        $return = false;

        if (strpos($range, '/') !== false) {
            //range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                //netmask is a 255.255.0.0 format
                $netmask     = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                $return      = ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
            } else {
                //netmask is a CIDR size block
                //fix the range argument
                $x = explode('.', $range);
                while(count($x)<4) $x[] = '0';
                list($a,$b,$c,$d) = $x;
                $range     = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
                $range_dec = ip2long($range);
                $ip_dec    = ip2long($ip);

                //Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                //$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

                //Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32-$netmask)) - 1;
                $netmask_dec  = ~ $wildcard_dec;

                $return      = (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            //range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !== false) { // a.b.*.* format
                //Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }

            if (strpos($range, '-') !== false) { // A-B format
                list($lower, $upper) = explode('-', $range, 2);

                $lower_dec = (float)sprintf("%u",ip2long($lower));
                $upper_dec = (float)sprintf("%u",ip2long($upper));
                $ip_dec    = (float)sprintf("%u",ip2long($ip));
                $return    = ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
            }
        }
        
        return $return;
    }	
	// --------------------------------------------------------------------------
}
/* End of file ip_restrict.php */
/* Location: ip_restrict/libraries/ip_restrict.php */