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
          echo $this ->linebreak(1).$this ->green('Took ' . $info['total_time']*1000 . ' ms to send a request to ' . $info['url']) ;
        }
        else
        {
          echo 'Curl error: ' . curl_error($tuCurl);
        }

        curl_close($tuCurl);

        return $tuData;
    }

    public function AssertURL($url, $data = null)
    {
        self::RegisterAssertion();

        echo $this ->linebreak(2) . $this -> blue('Verifying URL at '.$url) ;

        if($this ->setupCURL($url, $data))
        {
            echo $this ->linebreak(1) . $this -> green('URL: '.$url.' verified with AssertURL();') ;
            self::RegisterPass();
        }
        else
        {
            echo $this ->linebreak(1) . $this -> red('URL: unable to verify URL: '.$url.' with AssertURL();') ;
            self::RegisterFail();
        }
    }
}