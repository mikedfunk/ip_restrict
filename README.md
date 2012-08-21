ip_restrict
============================

Restrict access by an array of IP addresses


Setup
----------------------------

1. Install Sparks at [GetSparks.org](http://getsparks.org)
2. Edit **config/ip_restrict.php** to choose between whitelist or blacklist mode
3. Edit **config/ip_restrict.php** with your ip address whitelist or blacklist
4. Edit **config/ip_restrict.php** to choose between display error or redirect mode
5. Edit **config/ip_restrict.php** with your error message or redirect page

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

**1.1.0**

* Added blacklist option
* Renamed config variables
* Added redirect option

**1.0.0**

* Initial Release