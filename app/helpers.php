<?php

if (! function_exists('get_local_ip')) {
    /**
     * Get the local IP address of the machine
     */
    function get_local_ip(): string
    {
        if (app()->environment('local')) {
            // Try to get local IP address
            $localIp = null;
            
            // Method 1: Use hostname -I command (Linux/Mac)
            if (PHP_OS_FAMILY !== 'Windows') {
                $output = shell_exec('hostname -I 2>/dev/null');
                if ($output) {
                    $ips = explode(' ', trim($output));
                    $localIp = trim($ips[0]);
                }
            }
            
            // Method 2: Use ipconfig command (Windows)
            if (!$localIp && PHP_OS_FAMILY === 'Windows') {
                $output = shell_exec('ipconfig 2>nul');
                if ($output && preg_match('/IPv4.*?(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                    $localIp = $matches[1];
                }
            }
            
            // Method 3: Use network interfaces (PHP)
            if (!$localIp) {
                $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                if ($sock) {
                    // Connect to Google's DNS server to determine local IP
                    $result = @socket_connect($sock, '8.8.8.8', 53);
                    if ($result) {
                        socket_getsockname($sock, $localIp);
                    }
                    socket_close($sock);
                }
            }
            
            // Fallback to localhost
            return $localIp ?: '127.0.0.1';
        }
        
        return '127.0.0.1';
    }
}

if (! function_exists('get_dynamic_app_url')) {
    /**
     * Get the dynamic application URL for local development
     */
    function get_dynamic_app_url(int $port = 8000): string
    {
        if (app()->environment('local')) {
            $localIp = get_local_ip();
            return "http://{$localIp}:{$port}";
        }
        
        return config('app.url', 'http://localhost:8000');
    }
}

if (! function_exists('get_dynamic_vite_url')) {
    /**
     * Get the dynamic Vite dev server URL for local development
     */
    function get_dynamic_vite_url(int $port = 5173): string
    {
        if (app()->environment('local')) {
            $localIp = get_local_ip();
            return "http://{$localIp}:{$port}";
        }
        
        return "http://localhost:{$port}";
    }
}