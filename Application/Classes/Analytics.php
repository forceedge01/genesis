<?php

class Analytics extends AppMethods {

    private
            $connection;

    public function __construct() {

        if (TRACK_VISITS) {

            $this->connection = new Database();
            
            if(!$this->connection->TableExists('Tracks')){
                
                echo 'Table Tracks not found in database '.DBNAME.', cannot record tracks.';
                exit;
            }
        }
        else
            echo 'Tracking is disabled in configs but is being instantialized in application.';
    }

    /**
     *
     * @return mixed bool if false, otherwise the id of the track made
     * For this function to work you need to enable tracks in the analytics config<br>
     * and Create a table 'Tracks' in your database with the following definition<br><br>
     * id int(11) AUTO_INCREMENT<br>
     * IpAddress varchar(30)<br>
     * page varchar (100)<br>
     * userAgent varchar (150)<br>
     * time TIMESTAMP<br>
     * unique int(1)
     */
    public function recordTrack() {

        if (IGNORE_IP_ADDRESS)
            if ($this->variable ($_SERVER['REMOTE_ADDR'])->equals(IGNORE_IP_ADDRESS))
                return false;

        if (!$this->variable($_SERVER['HTTP_USER_AGENT'])->IsIn($this->bots())) {

            $request = new Request();

            if (!$request->getCookie('newUser')) {

                $this->connection->Table('Tracks')->SaveRecord(
                        
                        array(
                            'ipAddress' => $_SERVER['REMOTE_ADDR'],
                            'page' => $_SERVER['REQUEST_URI'],
                            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                            'time' => date('Y-m-d H:i:s'),
                            'unq' => '1',
                        )
                );
            } else if (!TRACK_UNIQUE_VISITS_ONLY) {

                $this->connection->Table('Tracks')->SaveRecord(
                        
                        array(
                            'ipAddress' => $_SERVER['REMOTE_ADDR'],
                            'page' => $_SERVER['REQUEST_URI'],
                            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                            'time' => date('Y-m-d H:i:s'),
                            'unq' => '0',
                        )
                );
            }

            $insert_id = $this->connection->GetInsertID();

            $request->setCookie('newUser', true);

            if ($insert_id) {

                return $insert_id;
            }
        }

        return false;
    }

    public function getTrack($id) {

        return $this->connection->Table('Tracks')->GetRecordBy($id)->GetResultSet();
    }

    public function getTracks() {

        return $this->connection->Table('Tracks')->GetRecords()->GetResultSet();
    }

    public function getTracksByIp($ip) {

        return $this->connection->Table('Tracks')->GetRecordBy(array('ipAddress' => $ip))->GetResultSet();
    }

    public function getTracksByBrowser($browser) {

        return $this->connection->Table('Tracks')->Where(array('userAgent' => $browser))->GetRecords()->GetResultSet();
    }

    public function getTrackBrowserReport() {

        return $this->connection->Table('Tracks')->Select(array('count(id) as HITS', 'userAgent'))->GroupBy(array('userAgent'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getTrackIpReport() {

        return $this->connection->Table('Tracks')->select(array('count(id) as Hits', 'IpAddress'))->GroupBy(array('IpAddress'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getTrackPageReport() {

        return $this->connection->Table('Tracks')->select(array('count(id) as Hits', 'page'))->groupBy(array('page'))->orderBy('Hits desc')->extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getUniqueVisits() {

        return $this->connection->Table('Tracks')->select(array('count(id) as Hits', 'page'))->Where(array('unq' => 1))->GroupBy(array('page'))->OrderBy('Hits desc')->Extra(array('distinct'))->execute()->GetResultSet();
    }

    public function getTotalVisits() {

        return $this->connection->Table('Tracks')->select(array('count(id) as Hits', 'page'))->GroupBy(array('page'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
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