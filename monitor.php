<?php
    
    class Monitor{

        public $url = "";
        public $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

        // returns true, if domain is availible, false if not
        // Reference: https://css-tricks.com/snippets/php/check-if-website-is-available/
        public function isDomainAvailible(){
            //initialize curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE,false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            // curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            //get answer
            $page = curl_exec($ch);
            echo curl_error($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if($httpcode >= 200 && $httpcode < 400) return true;
            else return false;
        }

    }