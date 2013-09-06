ip_restrict
============================

Restrict access by an array of IP addresses


Setup
----------------------------

1. Install Sparks at [GetSparks.org](http://getsparks.org)
2. Edit **config/ip_restrict.php**:
 1. Choose between _whitelist_ or _blacklist_ mode
 2. Edit your ip address _whitelist_ or _blacklist_
 3. Choose between _display\_error_ or _redirect_ mode
 4. Edit your _ip\_restrict\_error\_message_ or _ip\_restrict\_redirect\_url_

**IP Formats**

The format of the IP addresses in the whitelist and blacklist can either be a 
single IP address or one of the range formats.

* **Single Address:** `127.0.0.1`
* **Wildcard Format:** `192.168.1.*`
* **CIDR Format:** `192.168.1.0/24` or `192.168.1.0/255.255.255.0`
* **Start-End Format:** `192.168.1.0-192.168.1.255`

Usage
----------------------------

Load Spark 

    $this->load->spark('ip_restrict/x.x.x');

Restrict access the top of a controller method

    $this->ip_restrict->restrict();
    
Or in the constructor

    class Welcome extends CI_Controller
    {
    	public function __construct()
    	{
    		parent::__construct();
    		$this->load->spark('ip_restrict/x.x.x');
    		$this->ip_restrict->restrict();
    	}
    }
    
If the user's IP address is restricted based on whitelist or blacklist, it will either redirect or display the configured error message based the config. The remaining code will not be executed.

----------------------------

Changelog
----------------------------

**1.1.2**

* Add ability to use IP ranges in lists
* Clean up logic in restrict function

**1.1.1**

* Fixed incorrect logging
* Adjusted some comments
* Updated readme

**1.1.0**

* Added blacklist option
* Renamed config variables
* Added redirect option

**1.0.0**

* Initial Release