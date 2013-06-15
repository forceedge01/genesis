<?php

namespace Application\Components;

/**
 * Author: Wahab Qureshi.
 */

use Application\Core\Manager;

class Analytics extends Manager {

    private
            $connection,
            $request;

    public function __construct() {

        $this->request = new \Application\Core\Request();

        if (ANALYTICS_TRACK_VISITS) {

            $this->connection = new \Application\Core\Database();

            if(!$this->request->getCookie('visitIdentifier')){

                if(!$this->connection->TableExists(ANALYTICS_TRACK_TABLE)){

                    echo 'Table '.ANALYTICS_TRACK_TABLE.' not found in database '.DBNAME.', cannot record tracks. Please create table using this definition: <br /><br />
                        CREATE TABLE IF NOT EXISTS `'.DBNAME.'`.`'.ANALYTICS_TRACK_TABLE.'` (<br />

                        `id` INT(11) NOT NULL AUTO_INCREMENT,<br />
                        `ipAddress` VARCHAR(30),<br />
                        `page` VARCHAR (30),<br />
                        `userAgent` VARCHAR (100),<br />
                        `date` TIMESTAMP,<br />
                        `referer` varchar(100),<br />
                        `insiteActivity` TEXT,<br/>
                        `unq` INT (1),<br />
                        `ref` VARCHAR (30),<br />
                        PRIMARY KEY (`id`),<br />
                        KEY (`ref`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
                    exit;
                }
            }
        }
        else
            echo 'Tracking is disabled in configs but is being instantialized in application.';
    }

    /**
     *
     * @return mixed bool if false, otherwise the id of the track made.
     * Records a track (visit) from a user.
     */
    public function RecordTrack($OptionalReferenceId = null) {
        
        $this -> setTimedCookie();

        if (ANALYTICS_IGNORE_IP_ADDRESS)
            if ($this->variable ($_SERVER['REMOTE_ADDR'])->equals(ANALYTICS_IGNORE_IP_ADDRESS))
                return false;

        if (!$this->variable($_SERVER['HTTP_USER_AGENT'])->IsIn($this->bots())) {

            //Record for unqiue vists
            if (!$this->request->getCookie('visitIdentifier')) {

                if(!$this->connection->Table(ANALYTICS_TRACK_TABLE)->RecordExists(array('ipAddress' => $_SERVER['REMOTE_ADDR']))){

                    $this->connection->Table(ANALYTICS_TRACK_TABLE)->SaveRecord(

                        array(
                            'ipAddress' => mysql_real_escape_string($_SERVER['REMOTE_ADDR']),
                            'page' => mysql_real_escape_string($_SERVER['REQUEST_URI']),
                            'userAgent' => mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']),
                            'referer' => mysql_real_escape_string(@$_SERVER['HTTP_REFERER']),
                            'insiteActivity' => ':',
                            'time' => date('Y-m-d H:i:s'),
                            'unq' => '1',
                            'ref' => $OptionalReferenceId,
                        )
                    );
                }

                //Record for non-unique vistis
                else if (!ANALYTICS_TRACK_UNIQUE_VISITS_ONLY) {

                    $this->connection->Table(ANALYTICS_TRACK_TABLE)->SaveRecord(

                        array(
                            'ipAddress' => mysql_real_escape_string($_SERVER['REMOTE_ADDR']),
                            'page' => mysql_real_escape_string($_SERVER['REQUEST_URI']),
                            'userAgent' => mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']),
                            'referer' => mysql_real_escape_string(@$_SERVER['HTTP_REFERER']),
                            'insiteActivity' => ':',
                            'time' => date('Y-m-d H:i:s'),
                            'unq' => '0',
                            'ref' => $OptionalReferenceId
                        )
                    );
                }
            }

            //Record insite Track
            else if (ANALYTICS_RECORD_INSITE_TRACKS  && $this->request->isCookie ('lastVisitedURL') && strstr($this->request->getCookie('lastVisitedURL'), $_SERVER['HTTP_HOST']) && (!$this->variable($this->request->getCookie('lastVisitedURL'))->has(array($_SERVER['REQUEST_URI'])))){

                $this->connection->Table(ANALYTICS_TRACK_TABLE)->SaveRecord(

                    array(

                        'id' => $this->request->getCookie('visitIdentifier'),
                        'insiteActivity' => 'CONCAT('.DBNAME.'.Tracks.insiteActivity, "->'.mysql_real_escape_string($_SERVER['REQUEST_URI']).'('.time().')" )'
                    )
                );
            }

            $insert_id = $this->connection->GetInsertID();

            $this->request->setCookie('lastVisitedURL', 'http://'.$_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']);

            if($insert_id)
                $this->request->setCookie('visitIdentifier', $insert_id);

            return $this;
        }

        return false;
    }
    
    private function setTimedCookie()
    {
        $session = $this ->GetSessionManager();
        
        if(!$session->GetCookie('VisitAt'))
        {
            $session ->SetCookie('VisitAt', time());
        }
        else if(ANALYTICS_RESET_INTERVAL)
        {
            $this->resetTime();
        }
    }
    
    private function resetTime()
    {
        $session = $this ->GetSessionManager();
        
        $timeCheck = ANALYTICS_RESET_INTERVAL + $session ->GetCookie('VisistAt');
        
        if($timeCheck > time())
        {
            $session ->SetCookie('VisitAt', time());
            $session ->RemoveCookie('visitIdentifier');
        }
    }

    public function GetTrack($id) {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->GetRecordBy($id)->GetResultSet();
    }

    public function GetTracks() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->GetRecords()->GetResultSet();
    }

    public function GetTracksByIp($ip) {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->GetRecordBy(array('ipAddress' => $ip))->GetResultSet();
    }

    public function GetTracksByBrowser($browser) {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->Where(array('userAgent' => $browser))->GetRecords()->GetResultSet();
    }

    public function GetTrackBrowserReport() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->Select(array('count(id) as HITS', 'userAgent'))->GroupBy(array('userAgent'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function GetTrackIpReport() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'IpAddress'))->GroupBy(array('IpAddress'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function GetTrackPageReport() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'page'))->groupBy(array('page'))->orderBy('Hits desc')->extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getUniqueVisits() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'page'))->Where(array('unq' => 1))->GroupBy(array('page'))->OrderBy('Hits desc')->Extra(array('distinct'))->execute()->GetResultSet();
    }

    public function GetTotalVisits() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'page'))->GroupBy(array('page'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getBounces(){

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Bounces', 'page'))->GroupBy(array('page'))->OrderBy('Hits desc')->Where(array('insiteActivity' => ':'))->Execute()->GetResultSet();
    }

    public function getNumberOfBounces(){

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Bounces', 'page'))->GroupBy(array('page'))->OrderBy('Hits desc')->Where(array('insiteActivity' => ':'))->Execute()->GetNumberOfRows();
    }

    public function GetTrackStatistics(){

        $tracks = $this->connection->Query('select id, ipAddress, page, referer, insiteActivity, unq, date, userAgent, ref from '.ANALYTICS_TRACK_TABLE)->GetResultSet();

        $siteMap = array();

        $Visits = count($tracks);

        $tracks['Bounces'] = 0;
        $tracks['totalPageViews'] = 0;
        $tracks['totalUniquePageViews'] = 0;
        $tracks['totalUniqueVisits'] = 0;

        foreach($tracks as $track){

            $chunks = explode('->', $track->insiteActivity);

            if(isset($chunks[1]))
                unset($chunks[0]);
            else
                $tracks['Bounces'] += 1;

            foreach($chunks as $chunk){

                $page = explode('(', $chunk);

                if(!empty($page[1])){

                    $siteMap['PageViews'] += 1;
                    $siteMap['Pages']['VisitsFrom'][$page[0]]['Count'] += 1;
                    $siteMap['Pages']['VisitsFrom'][$page[0]]['Time'][] = array(

                        'TimeStamp' => $this->variable($page[1])->removeLastCharacter()->getVariableResult(),
                        'Date' => date('Y-m-d H:i:s', $this->variable($page[1])->removeLastCharacter()->getVariableResult()),
                    );
                }
            }

            $siteMap['UniquePageViews'] = count($siteMap['Pages']['VisitsFrom']);

            foreach($siteMap['Pages'] as $key => $counts){

                foreach($counts['VisitsFrom'] as $count){

                    $siteMap['Pages'][$key]['Count'] += $count;
                }
            }

            $track->insiteActivity = $siteMap;

            $tracks['totalPageViews'] += $track->insiteActivity['PageViews'];
            $tracks['totalUniquePageViews'] += $track->insiteActivity['UniquePageViews'];
            $tracks['totalUnqiueVisits'] += $track->unq;
        }

        $tracks['totalvisits'] = $Visits;

        return $tracks;
    }

    public function GetTrackSiteMap(){

        $tracks = $this->connection->Query('select id, ipAddress, page, referer, insiteActivity, unq, date, userAgent, ref from '.ANALYTICS_TRACK_TABLE)->GetResultSet();

        $siteMap = array();

        foreach($tracks as $track){

            $chunks = explode('->', $track->insiteActivity);
            $pages = array();
            unset($chunks[0]);

            $index = 0;

            foreach($chunks as $page){

                $p = explode('(', $page);

                $pages[$index] = array(

                    'view' => $p[0],
                    'time' => $this->variable($p[1])->removeLastCharacter()->getVariableResult(),
                );

                if($index > 0){

                    $pages[($index-1)]['intervalSeconds'] = ($pages[$index]['time'] - $pages[($index-1)]['time'])/10;
                }

                $index++;
            }

            $siteMap[] = array(

                'ipAddress' => $track->ipAddress,
                'Referer' => $track->referer,
                'date' => $track->date,
                'Unique' => $this->unq,
                'Browser' => $track->userAgent,
                'ref' => $track->ipAddress,
                'Visit' => $pages
            );
        }

        return $siteMap;
    }

    private function bots() {

        return array(
            'google', 'bing', 'yahoo', 'bot', 'crawler', 'baiduspider', 'bingbot', 'msn', 'abacho', 'abcdatos',
            'alkalinebot', 'almaden', 'altavista', 'antibot', 'anzwerscrawl', 'aol', 'search', 'appie', 'arachnoidea',
            'araneo', 'architext', 'ariadne', 'arianna', 'ask', 'jeeves', 'aspseek', 'asterias', 'astraspider', 'atomz',
            'augurfind', 'backrub', 'baiduspider', 'bannana_bot', 'bbot', 'bdcindexer', 'blindekuh', 'boitho', 'boito',
            'borg-bot', 'bsdseek', 'christcrawler', 'computer_and_automation_research_institute_crawler', 'coolbot',
            'cosmos', 'crawler', 'crawler@fast', 'crawlerboy', 'cruiser', 'cusco', 'cyveillance', 'deepindex', 'denmex',
            'dittospyder', 'docomo', 'dogpile', 'dtsearch', 'elfinbot', 'entire', 'web', 'esismartspider', 'exalead',
            'excite', 'ezresult', 'fast', 'fast-webcrawler', 'fdse', 'felix', 'fido', 'findwhat', 'finnish', 'firefly',
            'firstgov', 'fluffy', 'freecrawl', 'frooglebot', 'galaxy', 'gaisbot', 'geckobot', 'gencrawler', 'geobot',
            'gigabot', 'girafa', 'goclick', 'goliat', 'googlebot', 'griffon', 'gromit', 'grub-client', 'gulliver',
            'gulper', 'henrythemiragorobot', 'hometown', 'hotbot', 'htdig', 'hubater', 'ia_archiver', 'ibm_planetwide',
            'iitrovatore-setaccio', 'incywincy', 'incrawler', 'indy', 'infonavirobot', 'infoseek', 'ingrid', 'inspectorwww',
            'intelliseek', 'internetseer', 'ip3000.com-crawler', 'iron33', 'jcrawler', 'jeeves', 'jubii', 'kanoodle',
            'kapito', 'kit_fireball', 'kit-fireball', 'ko_yappo_robot', 'kototoi', 'lachesis', 'larbin', 'legs',
            'linkwalker', 'lnspiderguy', 'look.com', 'lycos', 'mantraagent', 'markwatch', 'maxbot', 'mercator', 'merzscope',
            'meshexplorer', 'metacrawler', 'mirago', 'mnogosearch', 'moget', 'motor', 'muscatferret', 'nameprotect',
            'nationaldirectory', 'naverrobot', 'nazilla', 'ncsa', 'beta', 'netnose', 'netresearchserver', 'ng/1.0',
            'northerlights', 'npbot', 'nttdirectory_robot', 'nutchorg', 'nzexplorer', 'odp', 'openbot', 'openfind',
            'osis-project', 'overture', 'perlcrawler', 'phpdig', 'pjspide', 'polybot', 'pompos', 'poppi', 'portalb',
            'psbot', 'quepasacreep', 'rabot', 'raven', 'rhcs', 'robi', 'robocrawl', 'robozilla', 'roverbot', 'scooter',
            'scrubby', 'search.ch', 'search.com.ua', 'searchfeed', 'searchspider', 'searchuk', 'seventwentyfour',
            'sidewinder', 'sightquestbot', 'skymob', 'sleek', 'slider_search', 'slurp', 'solbot', 'speedfind', 'speedy',
            'spida', 'spider_monkey', 'spiderku', 'stackrambler', 'steeler', 'suchbot', 'suchknecht.at-robot', 'suntek',
            'szukacz', 'surferf3', 'surfnomore', 'surveybot', 'suzuran', 'synobot', 'tarantula', 'teomaagent', 'teradex',
            't-h-u-n-d-e-r-s-t-o-n-e', 'tigersuche', 'topiclink', 'toutatis', 'tracerlock', 'turnitinbot', 'tutorgig',
            'uaportal', 'uasearch.kiev.ua', 'uksearcher', 'ultraseek', 'unitek', 'vagabondo', 'verygoodsearch', 'vivisimo',
            'voilabot', 'voyager', 'vscooter', 'w3index', 'w3c_validator', 'wapspider', 'wdg_validator', 'webcrawler',
            'webmasterresourcesdirectory', 'webmoose', 'websearchbench', 'webspinne', 'whatuseek', 'whizbanglab', 'winona',
            'wire', 'wotbox', 'wscbot', 'www.webwombat.com.au', 'xenu', 'link', 'sleuth', 'xyro', 'yahoobot', 'yahoo!',
            'slurp', 'yandex', 'yellopet-spider', 'zao/0', 'zealbot', 'zippy', 'zyborg', 'mediapartners-google', 'abcsearch',
            'acoon', 'adsarobot', 'aesop', 'ah-ha',
        );
    }

}