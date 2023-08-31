<?php

declare(strict_types=1);

/*
 * Copyright (c) 2021 Kenji Suzuki
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/kenjis/ci3-to-4-upgrade-helper
 */

namespace Kenjis\CI3Compatible\Core;

use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

class CI_Input
{
    
    /**
     * IP address of the current user
     *
     * @var	string
     */
    protected $ip_address = FALSE;
    
    /**
     * Fetch an item from the GET array
     *
     * @param   mixed $index     Index for item to be fetched from $_GET
     * @param   bool  $xss_clean Whether to apply XSS filtering
     *
     * @return  mixed
     */
    public function get($index = null, bool $xss_clean = false)
    {
        $this->checkXssClean($xss_clean);

        $request = Services::request();

        return $request->getGet($index);
    }

    /**
     * Fetch an item from the POST array
     *
     * @param   mixed $index     Index for item to be fetched from $_POST
     * @param   bool  $xss_clean Whether to apply XSS filtering
     *
     * @return  mixed
     */
    public function post($index = null, bool $xss_clean = false)
    {
        $this->checkXssClean($xss_clean);

        $request = Services::request();

        return $request->getPost($index);
    }

    private function checkXssClean(bool $xss_clean)
    {
        if ($xss_clean !== false) {
            throw new NotSupportedException(
                '$xss_clean is not supported.'
                . ' Preventing XSS should be performed on output, not input!'
                . ' Use esc() <https://codeigniter4.github.io/CodeIgniter4/general/common_functions.html#esc> instead.'
            );
        }
    }
    
    /**
     * Fetch an item from the COOKIE array
     *
     * @param	mixed	$index		Index for item to be fetched from $_COOKIE
     * @param	bool	$xss_clean	Whether to apply XSS filtering
     * @return	mixed
     */
    public function cookie($index = NULL, $xss_clean = false)
    {
        $this->checkXssClean($xss_clean);
        
        $request = Services::request();
        
        return $request->getCookie($index);
    }

    /**
     * Fetch an item from the SERVER array
     *
     * @param   mixed $index     Index for item to be fetched from $_SERVER
     * @param   bool  $xss_clean Whether to apply XSS filtering
     *
     * @return  mixed
     */
    public function server($index = null, bool $xss_clean = false)
    {
        $this->checkXssClean($xss_clean);

        $request = Services::request();

        return $request->getServer($index);
    }
    
    /**
     * Fetch the IP Address
     *
     * Determines and validates the visitor's IP address.
     *
     * @return	string	IP address
     */
    public function ip_address()
    {
        if ($this->ip_address !== FALSE)
        {
            return $this->ip_address;
        }
    
        $config = config('App');
        $proxy_ips = $config->proxyIPs;
        if ( ! empty($proxy_ips) && ! is_array($proxy_ips))
        {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
        }
    
        $this->ip_address = $this->server('REMOTE_ADDR');
    
        if ($proxy_ips)
        {
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header)
            {
                if (($spoof = $this->server($header)) !== NULL)
                {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    sscanf($spoof, '%[^,]', $spoof);
    
                    if ( ! $this->valid_ip($spoof))
                    {
                        $spoof = NULL;
                    }
                    else
                    {
                        break;
                    }
                }
            }
    
            if ($spoof)
            {
                for ($i = 0, $c = count($proxy_ips); $i < $c; $i++)
                {
                    // Check if we have an IP address or a subnet
                    if (strpos($proxy_ips[$i], '/') === FALSE)
                    {
                        // An IP address (and not a subnet) is specified.
                        // We can compare right away.
                        if ($proxy_ips[$i] === $this->ip_address)
                        {
                            $this->ip_address = $spoof;
                            break;
                        }
    
                        continue;
                    }
    
                    // We have a subnet ... now the heavy lifting begins
                    isset($separator) OR $separator = $this->valid_ip($this->ip_address, 'ipv6') ? ':' : '.';
    
                    // If the proxy entry doesn't match the IP protocol - skip it
                    if (strpos($proxy_ips[$i], $separator) === FALSE)
                    {
                        continue;
                    }
    
                    // Convert the REMOTE_ADDR IP address to binary, if needed
                    if ( ! isset($ip, $sprintf))
                    {
                        if ($separator === ':')
                        {
                            // Make sure we're have the "full" IPv6 format
                            $ip = explode(':',
                                str_replace('::',
                                    str_repeat(':', 9 - substr_count($this->ip_address, ':')),
                                    $this->ip_address
                                )
                            );
    
                            for ($j = 0; $j < 8; $j++)
                            {
                                $ip[$j] = intval($ip[$j], 16);
                            }
    
                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        }
                        else
                        {
                            $ip = explode('.', $this->ip_address);
                            $sprintf = '%08b%08b%08b%08b';
                        }
    
                        $ip = vsprintf($sprintf, $ip);
                    }
    
                    // Split the netmask length off the network address
                    sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);
    
                    // Again, an IPv6 address is most likely in a compressed form
                    if ($separator === ':')
                    {
                        $netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
                        for ($j = 0; $j < 8; $j++)
                        {
                            $netaddr[$j] = intval($netaddr[$j], 16);
                        }
                    }
                    else
                    {
                        $netaddr = explode('.', $netaddr);
                    }
    
                    // Convert to binary and finally compare
                    if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0)
                    {
                        $this->ip_address = $spoof;
                        break;
                    }
                }
            }
        }
    
        if ( ! $this->valid_ip($this->ip_address))
        {
            return $this->ip_address = '0.0.0.0';
        }
    
        return $this->ip_address;
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Validate IP Address
     *
     * @param	string	$ip	IP address
     * @param	string	$which	IP protocol: 'ipv4' or 'ipv6'
     * @return	bool
     */
    public function valid_ip($ip, $which = '')
    {
        switch (strtolower($which))
        {
            case 'ipv4':
                $which = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = 0;
                break;
        }
    
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, $which);
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Fetch User Agent string
     *
     * @return	string|null	User Agent string or NULL if it doesn't exist
     */
    public function user_agent($xss_clean = false)
    {
        return $this->server('HTTP_USER_AGENT', $xss_clean);
    }
}
