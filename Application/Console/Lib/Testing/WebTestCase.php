<?php

namespace Application\Console;



class WebTestCase extends TemplateTestCase{

    public function crawlURL($url, $data = null)
    {
        echo $this ->linebreak(2) . $this -> blue('Initiating URL crawl at '.$url) ;

        return $this ->setupCURL($url, urlencode($data)) ;
    }

    private function setupCURL($url, $data = null)
    {
        $info = array();

        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $url);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($tuCurl, CURLOPT_HEADER, false);
        curl_setopt($tuCurl, CURLOPT_HTTP200ALIASES, array(200, 301, 302));

//        curl_setopt($tuCurl, CURLOPT_PORT , 443);
//        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
//        curl_setopt($tuCurl, CURLOPT_SSLVERSION, 3);
//        curl_setopt($tuCurl, CURLOPT_SSLCERT, getcwd() . "/client.pem");
//        curl_setopt($tuCurl, CURLOPT_SSLKEY, getcwd() . "/keyout.pem");
//        curl_setopt($tuCurl, CURLOPT_CAINFO, getcwd() . "/ca.pem");

        if($data)
        {
            curl_setopt($tuCurl, CURLOPT_POST, 1);
            curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
        }
//        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 1);
//        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","SOAPAction: \"/soap/action/query\"", "Content-length: ".strlen($data)));

        $tuData = curl_exec($tuCurl);

        $httpCode = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);

        if($httpCode == 404) {

            curl_close($tuCurl);
            return false;
        }

        else if(!curl_errno($tuCurl))
        {
          $info = curl_getinfo($tuCurl);
          $info['message'] = ('Took ' . $info['total_time']*1000 . ' ms and '.$info['redirect_count'].' redirect(s) to send a request to ' . $info['url'] . ', Returned status '.$info['http_code']);
        }
        else
        {
          echo 'Curl error: ' . curl_error($tuCurl);
        }

        curl_close($tuCurl);

        return $info;
    }

    public function AssertURL($url, $data = null)
    {
        $info = $this ->setupCURL($url, $data);

        if($info)
        {
            self::RegisterPass($this -> green(__FUNCTION__ . '(); '.$url.' verified'), $info['message']);
        }
        else
        {
            self::RegisterFail($this -> red(__FUNCTION__ . '(); unable to verify URL: '.$url));
        }
    }

    public function AssertRedirect($url, $redirectUrl)
    {
        $info = $this->setupCURL($url, false);

        if(strtolower($redirectUrl) == strtolower($info['url']))
        {
            $message = __FUNCTION__ . '(); Redirect test '.$url.' => '.$redirectUrl.' passed';
            self::RegisterPass($this->green($message), $info['message']);

            return true;
        }

        self::RegisterFail($this->red(__FUNCTION__ . '(); Redirect test failed, got '.$url.' => '.$info['url'].' Instead of '.$redirectUrl));

        return false;
    }

    public function AssertFlashMessage($method, $message, $args = null)
    {
        session_start();
        self::$testClass->$method($args);

        if(array_search($message, $_SESSION['FlashMessages']) !== false)
            return self::RegisterPass ('Flash Message found');

        return self::RegisterFail('Unable to find flash message setup for method: '.$method);
    }
}