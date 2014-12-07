<?php

namespace Application\Components\Cloner;

/**
 * Author: Wahab Qureshi.
 * Depreciated? Works only with wp-contents where as that was a separate module, re-think this logic?
 */

use Application\Core\DatabaseManager;

class Cloner extends DatabaseManager {

    public
            $domain,
            $db,
            $verbose,
            $portal_id,
            $product_id,
            $merchant_id,
            $scenario,
            $path;

    /**
     *
     * @return boolean - returns true if success, false if failure.
     * <br /><br />Verifies if the domain passed on is a valid domain if specified to do so in Cloner config,<br />
     * checks if site directory already exists, if it does will show a repair link.
     */
    private function VerifySite() {

        if (ALLOW_ONLY_FANDIST_SUBDOMAINS) {

            if (strpos($this->domain, '.fandistribution.com') > 0) {

            } else {

                $this->setError(array('Failure' => 'You can only create .fandistribution.com subdomains.'));

                return false;
            }
        }

        if (is_dir(SITES_FOLDER . $this->domain)) {

            $site = new Site();

            $site->GetByDomain($this->domain);

            if (!isset($site->id))
                $this->setError(array('Failure' => 'Unable to build site, directory already exists. <a href="' . $this->setRoute('Site_Repair', array('domain' => $this->domain)) . '">Click here to attempt to repair.</a>'));
            else
                $this->setError(array('Failure' => 'Site already exists.'));

            return false;
        }

        return true;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Will create a new site where defined in the Cloner config.
     *
     */
    public function CloneSite($scenario = 'live') {

        if ($scenario == 'test') {

            $this->domain = 'testCandidate';

            if(is_dir(SITES_FOLDER . $this->domain))
                @$this->removeClone('testCandidate');

            if ($this->CreateDirectory()) {

                if ($this->CopyMaterial()) {

                    if ($this->ExpandClone()) {

                        if ($this->setConfig()) {

                            if ($this->AlterConfigsAndImportSQL()) {

                                $this->Cleanup();

                                return true;
                            }
                            else
                                $this->setError(array('Failure' => 'Unable to alter and import SQL'));
                        }
                        else
                            $this->setError(array('Failure' => 'Unable to alter wp-config file, file not found'));
                    }
                    else
                        $this->setError(array('Failure' => 'Unable to unzip clone'));
                }
                else
                    $this->setError(array('Failure' => 'Unable to copy material to the new site.'));
            }
            else
                $this->setError(array('Failure'=>'Site already exists'));
        }
        else {

            if (!empty($this->domain)) {

                if ($this->VerifySite()) {

                    if ($this->CreateDirectory()) {

                        if ($this->CopyMaterial()) {

                            if ($this->ExpandClone()) {

                                if ($this->setConfig()) {

                                    if ($this->AlterConfigsAndImportSQL()) {

                                        $this->Cleanup();

                                        return true;
                                    }
                                    else
                                        $this->setError(array('Failure' => 'Unable to alter and import SQL'));
                                }
                                else
                                    $this->setError(array('Failure' => 'Unable to alter wp-config file, file not found'));
                            }
                            else
                                $this->setError(array('Failure' => 'Unable to unzip clone'));
                        }
                        else
                            $this->setError(array('Failure' => 'Unable to copy material to the new site.'));
                    }
                    else
                        $this->setError(array('Failure' => 'Unable to create directory'));

                    $directory = new Dir();

//                $directory->removeDirectory(SITES_FOLDER . $this->domain);
                }
            }
            else
                $this->setError(array('Failure' => 'You must assign a domain name to build to the cloner.'));

            return false;
        }
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Copies the site material over to the destination site for extraction. Site material and destination sites can be defined in the Cloner config.
     */
    private function CopyMaterial() {

        try {

            $path = null;

            if($this->domain == 'testCandidate'){

                $path = TEST_CANDIDATE_DIR;

                $this->SetFlash('Creating Test Candidate from: ' . $path . STREETTEAM_ZIP_NAME);
            }
            else
                $path = SITE_MATERIAL_FOLDER;

            if ($this->verbose)
                error_log('copying over site material .zip ...');
            copy($path . STREETTEAM_ZIP_NAME, SITES_FOLDER . $this->domain . '/' . STREETTEAM_ZIP_NAME);

            if ($this->verbose)
                error_log('copying over .sql ...');
            copy(SITE_MATERIAL_FOLDER . CLONE_SQL_NAME, SITES_FOLDER . $this->domain . '/' . CLONE_SQL_NAME);

            return true;
        } catch (Exception $e) {

            echo $e->getMessage();
        }
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Create an empty directory for the domain.
     */
    private function CreateDirectory() {

        if ($this->verbose)
            error_log('creating directory ' . $this->domain . '...');
        if (mkdir(SITES_FOLDER . $this->domain))
            return true;
        else
            return false;
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Alters the new wordpress site's config file with new credentials.
     */
    private function setConfig() {

        //wp-config of new file to be edited here.

        $filename = '/wp-config.php';

        if ($this->verbose)
            error_log('Editing file ' . SITES_FOLDER . $this->domain . $filename . ' ...');

        if ($file = file_get_contents(SITES_FOLDER . $this->domain . $filename)) {

            $file = str_replace("'DB_NAME', 'wordpress'", "'DB_NAME', '" . $this->domain . "Multisites'", $file);

            $file = str_replace("'DB_USER', 'root'", "'DB_USER', '" . NEW_WP_DB_USER . "'", $file);

            $file = str_replace("'DB_PASSWORD', 'root'", "'DB_PASSWORD', '" . NEW_WP_DB_PASSWORD . "'", $file);

            file_put_contents(SITES_FOLDER . $this->domain . $filename, $file);

            return true;
        }
        else
            return false;
    }

    /**
     * Cleans up the site material files from the newly created wordpress directory.
     */
    private function Cleanup() {

        if ($this->verbose)
            error_log('Removing .zip file....');
        unlink(SITES_FOLDER . $this->domain . '/' . STREETTEAM_ZIP_NAME);

        if ($this->verbose)
            error_log('Removing .sql file....');
        unlink(SITES_FOLDER . $this->domain . '/' . CLONE_SQL_NAME);
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Will unzip files of the zip copied over from the site material folder
     */
    private function ExpandClone() {

        $zip = new Zip(SITES_FOLDER . $this->domain . '/' . STREETTEAM_ZIP_NAME);

        if ($this->verbose)
            error_log('Extracting files from ' . $this->domain);

        if ($zip->unzip(SITES_FOLDER . $this->domain)) {

            return true;
        }
        else
            return false;
    }

    /**
     * Alters the wp-config file of the new wordpress site (setConfig used), imports the new sql schema for the site into mysql. Transaction based.
     */
    private function AlterConfigsAndImportSQL() {

        if ($this->verbose)
            error_log('Altering wordpress wp-config file...');
        $this->setConfig($this->domain, SITES_FOLDER . $this->domain . '/wp-config.php');

        if ($this->verbose)
            error_log('Connecting to the database....');

        $db = new Database();

        $db->verbose = $this->verbose;

        $db->domain = $this->domain;

        $db->BeginTransaction();

        if ($this->verbose)
            error_log('Creating new wordpress database and setting up initial schema....');
        $this->createWPDB();

        if ($this->verbose)
            error_log('Inserting record into WPBuilder database....');
        $this->registerSite();

        if ($this->verbose)
            error_log('Committing all changes....');
        $db->Commit();

        return true;
    }

    /**
     *
     * @param int $id - The Site id as registered by the cloner in mysql Sites table.
     * @return boolean - true on success, false on failure<br />
     * <br />Removes a clone created by the cloner
     */
    public function removeClone($id) {

        try {

            $db = new Database();

            $error = false;

            if(is_numeric($id)){

                $sql = "select site from Sites where id = $id";

            }
            else{

                $sql = "select site from Sites where site = '$id'";
            }

            $db->Query($sql);

            $domain = $db->GetFirstResult()->site;

            if ($domain) {

                if (is_dir(SITES_FOLDER . $domain)) {

                    $directory = new Dir();

                    if (!$directory->removeDirectory(SITES_FOLDER . $domain)) {

                        trigger_error('unable to remove directory');

                        $error = true;
                    } else {

                        $db->queries[] = 'use `' . $domain . 'Multisites`';

                        $db->queries[] = "show tables";

                        $db->multiQuery();

                        $results = $db->GetFirstResult();

                        foreach ($results as $result) {

                            foreach ($result as $table) {
                                $db->queries[] = "drop table if exists ` " . $domain . "`.`" . $table . "`";
                            }
                        }

                        $db->queries[] = 'drop database `' . $domain . 'Multisites`';

                        $db->queries[] = "REVOKE all ON `" . $domain . "Multisites`.* FROM '" . NEW_WP_DB_USER . "'@'" . NEW_WP_DB_HOST . "'";

                        $db->queries[] = 'use `' . DBNAME . '`';

                        if(is_numeric($id))
                            $db->queries[] = 'delete from Sites where id = ' . $id;
                        else
                            $db->queries[] = 'delete from Sites where site = "' . $id . '"';

                        if (!$db->multiQuery()) {

                            trigger_error('Could not remove database entries, resort to manual cleanup. Aborting');

                            $error = true;
                        }
                    }
                } else {

                    $sql = "delete from Sites where id = $id";

                    $db->Query($sql);

                    trigger_error('Could not remove database entries, resort to manual cleanup. Aborting');

                    $error = true;
                }
            } else {

                trigger_error('Specified domain could not be found in the database, you will have to resort to manual removal.');

                $error = true;
            }

            if ($error)
                return false;
            else
                return true;
        } catch (Exception $e) {

            trigger_error($e->getMessage());
        }
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />Creates a new wordpress database and adds a user to its previleges.
     */
    public function createWPDB() {

        try {
            if ($this->verbose)
                error_log('Creating database for WP site...<br /><br />');
            $sql = "create database if not exists `{$this->domain}Multisites`";

            $this->Query($sql);

            if ($this->verbose)
                error_log('Granting permissions on the database...<br /><br />');
            $sql = "grant all on `{$this->domain}Multisites`.* to '" . NEW_WP_DB_USER . "'@'" . NEW_WP_DB_HOST . "' identified by '" . NEW_WP_DB_PASSWORD . "'";

            $this->Query($sql);

            if ($this->verbose)
                error_log('Verifying creation and permissions...<br /><br />');
            if ($this->verifyDB()) {

                if ($this->verbose)
                    error_log('Populating Database...<br /><br />');
                $this->configureDB();
            }

            $sql = "use `" . DBNAME . "`";

            $this->Query($sql);

            return true;
        } catch (Exception $e) {

            $this->RollBack();

            $site = new SiteController();

            $site->removeDirectory(SITES_FOLDER . $this->domain);

            echo 'Error in createWPDB: ' . $e->getMessage();
        }
    }

    /**
     *
     * @return boolean - true on success, false on failure<br />
     * <br />
     */
    private function verifyDB() {

        return true;
    }

    /**
     * Alters the .sql file to change the base address over to the new domain.
     */
    private function configureDB() {

        try {

            if ($this->verbose)
                error_log('Re-configuring database file to import...<br /><br />');

            $file = file_get_contents(SITES_FOLDER . $this->domain . '/' . CLONE_SQL_NAME);

            $file = str_replace("http://st-wordpress", WP_SITE_BASE . 'Sites/' . $this->domain, $file);

            if (USE_DEFAULT_API_CREDENTIALS) {

                $this->portal_id = DEFAULT_PORTAL_ID;

                $this->merchant_id = DEFAULT_MERCHANT_ID;

                $this->product_id = DEFAULT_PRODUCT_ID;
            } else {

                ini_set("soap.wsdl_cache_enabled", 0);

                $client = new SoapClient('http://dev.api.fandistribution.com/api.php?wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));

                $headers[] = new SoapHeader('http://dev.api.fandistribution.com/api.php', 'Authenticate', array(API_KEY), true);

                $client->__setSoapHeaders($headers);

                $domain = str_replace('.', '', $this->domain);
                $domain = str_replace('-', '', $domain);

                $apiCreds = $client->createStack($domain);

                unset($client);

                $this->portal_id = $apiCreds['portalId'];

                $this->merchant_id = $apiCreds['merchantId'];

                $this->product_id = $apiCreds['productId'];
            }

            $file = str_replace("'lb_sh_portal_id', '1'", "'lb_sh_portal_id', '{$this->portal_id}'", $file);

            $file = str_replace("'lb_sh_merchant_id', '1'", "'lb_sh_merchant_id', '{$this->merchant_id}'", $file);

            $file = str_replace("'lb_sh_product_id', '1'", "'lb_sh_product_id', '{$this->product_id}'", $file);

            if (DEFAULT_WP_ADMIN_USERNAME != 'admin')
                $file = str_replace("1, 'admin'", "1, '" . DEFAULT_WP_ADMIN_USERNAME . "'", $file);

            file_put_contents(SITES_FOLDER . $this->domain . '/' . CLONE_SQL_NAME, $file);

            //import sql into db here.

            if ($this->verbose)
                error_log('Init Import into MySQL...<br /><br />');

            $this->importSQL();

        } catch (Exception $e) {

            $this->RollBack();

            trigger_error('Error in configureDB: ' . $e->getMessage());
        }
    }

    /**
     * Imports the .sql file into mysql
     */
    public function importSQL($sqlpath = null) {

        try {

            if ($this->verbose)
                error_log('Switching to new site Database...<br /><br />');

            $sql = "use `{$this->domain}Multisites`";

            $this->Query($sql);

            if (!$sqlpath)
                $file = file_get_contents(SITES_FOLDER . $this->domain . '/' . CLONE_SQL_NAME);
            else
                $file = file_get_contents($sqlpath);

            $queries = explode('--*--', $file);

            if ($this->verbose)
                error_log('Importing SQL data...<br /><br />');

            foreach ($queries as $query) {

                $this->Query($query);
            }
        } catch (Exception $e) {

            trigger_error('Error in importSQL: ' . $e->getMessage());
        }
    }

    /**
     * Inserts a record of the new site into mysql
     */
    public function registerSite() {

        try {

            $site = new Site();

            $site->site = $this->domain;

            $site->url = $this->domain;

            $site->portalId = $this->portal_id;

            $site->merchantId = $this->merchant_id;

            $site->productId = $this->product_id;

            $site->create();
        } catch (Exception $e) {
            echo 'Unable to register site: ' . $e->getMessage();
        }
    }

}