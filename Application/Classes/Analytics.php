<?php

class Analytics extends AppMethods {

    private
            $connection,
            $request;

    public function __construct() {

        $this->request = new Request();

        if (ANALYTICS_TRACK_VISITS) {

            $this->connection = new Database();

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
    public function recordTrack($OptionalReferenceId = null) {

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
                            'insiteActivity' => mysql_real_escape_string($_SERVER['REQUEST_URI']),
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
                            'insiteActivity' => mysql_real_escape_string($_SERVER['REQUEST_URI']),
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
                        'insiteActivity' => 'CONCAT('.DBNAME.'.Tracks.insiteActivity, "->'.mysql_real_escape_string($_SERVER['REQUEST_URI']).'" )'
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

    public function getTrack($id) {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->GetRecordBy($id)->GetResultSet();
    }

    public function getTracks() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->GetRecords()->GetResultSet();
    }

    public function getTracksByIp($ip) {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->GetRecordBy(array('ipAddress' => $ip))->GetResultSet();
    }

    public function getTracksByBrowser($browser) {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->Where(array('userAgent' => $browser))->GetRecords()->GetResultSet();
    }

    public function getTrackBrowserReport() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->Select(array('count(id) as HITS', 'userAgent'))->GroupBy(array('userAgent'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getTrackIpReport() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'IpAddress'))->GroupBy(array('IpAddress'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getTrackPageReport() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'page'))->groupBy(array('page'))->orderBy('Hits desc')->extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getUniqueVisits() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'page'))->Where(array('unq' => 1))->GroupBy(array('page'))->OrderBy('Hits desc')->Extra(array('distinct'))->execute()->GetResultSet();
    }

    public function getTotalVisits() {

        return $this->connection->Table(ANALYTICS_TRACK_TABLE)->select(array('count(id) as Hits', 'page'))->GroupBy(array('page'))->OrderBy('Hits desc')->Extra(array('distinct'))->Execute()->GetResultSet();
    }

    public function getTrackReport(){

        $tracks = $this->connection->Query('select id, page, referer, insiteActivity from '.ANALYTICS_TRACK_TABLE)->GetResultSet();

        $this->pre($tracks);

        $siteMap = array();

        foreach($tracks as $track){

            $chunks = explode('->', $track->insiteActivity);

            $siteMap['numberOfPagesVisited'] = count($chunks);
            $siteMap['LeastVisitedPage']['Count'] = 10000000;
            $siteMap['MostVisitedPage']['Count'] = 0;

            foreach($chunks as $chunk){

                $page = explode('=>', $chunk);

                if(!empty($page[0])){

                    $siteMap['Pages'][$page[1]]['VisitsFrom'][$page[0]] += 1;
                }
            }

            foreach($siteMap['Pages'] as $key => $counts){

                foreach($counts['VisitsFrom'] as $count){

                    $siteMap['Pages'][$key]['Count'] += $count;

                    if($count < $siteMap['LeastVisitedPage']['Count']){

                        $siteMap['LeastVisitedPage']['Count'] = $count;
                        $siteMap['LeastVisitedPage']['Page'] = $key;
                    }

                    if($count > $siteMap['MostVisitedPage']['Count']){

                        $siteMap['MostVisitedPage']['Count'] = $count;
                        $siteMap['MostVisitedPage']['Page'] = $key;
                    }
                }
            }
        }

//        $this->pre($siteMap);
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