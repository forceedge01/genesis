<?php

class Zip extends ZipArchive {

    private
            $files = array(),
            $zip,
            $domain;

    public function __construct($zip, $create = false) {

        try {
            $this->bool = false;

            $this->zip = new ZipArchive();

            if ($create)
                $result = $this->zip->open($zip, ZIPARCHIVE::CREATE);
            else
                $result = $this->zip->open($zip);

            if ($result === true)
                return true;
            else {

                trigger_error('Unable to open zip archive. ' . $result . $zip);
                return false;
            }
        } catch (Exception $e) {

            echo $e->getMessage();
        }
    }

    /**
     *
     * @param type $directory
     * Add a directory to the opened zip file.
     */
    public function addDirectory($directory) {

        try {
            $dir = $directory;

            if (strpos($dir, '/Sites/') != -1) {

                $dir = explode('/Sites/', $dir);

                end($dir);

                $dir = $dir[key($dir)];
            }

            $this->zip->addEmptyDir($dir);

            $nodes = glob($directory . '/*');

            foreach ($nodes as $node) {

                if (is_dir($node)) {

                    $this->addDirectory($node);
                } else if (is_file($node)) {

                    $file = explode('/', $node);

                    end($file);

                    $this->zip->addFile($node, $dir . '/' . $file[key($file)]);
                }
            }

            return true;
        }
        catch (Exception $e) {

            trigger_error($e->getMessage());
        }
    }

    /**
     * close the zip file
     */
    public function Close() {

        $this->zip->close();
    }

    /**
     *
     * @param type $destination
     * @return boolean  - true on success, false on failure<br />
     * <br />Unzip files from an opened zip file
     */
    public function unzip($destination, $exceptionEntry = null) {

        if (is_object($this->zip)) {

            $files = null;

            if(!empty($exceptionEntry))
            {

                for($i = 0; $i < $this->zip->numFiles; $i++) {

                    foreach($exceptionEntry as $exception){

                        if($this->zip->getNameIndex($i) != $exception)
                            $files[] = $this->zip->getNameIndex($i);
                    }
                }

            }

            if ($this->zip->extractTo($destination, $files)) {

                if ($this->zip->close())
                    return true;
                else {

                    trigger_error('Unable to close zip file.');
                    return false;
                }
            } else {

                trigger_error('Unable to extract file.');
                return false;
            }
        } else {

            trigger_error('Couldn\'t Unzip file, zip empty, aborting ');
            return false;
        }
    }

    /**
     *
     * @param type $directory
     * @return boolean -  - true on success, false on failure<br />
     * <br />Create a zip file from a directory
     */
    public function ZipFromDirectory($directory) {

        try {

            if ($this->addDirectory($directory)) {

                $this->Close();

                return true;
            }
            else
                return false;
        } catch (Exception $e) {

            trigger_error('Unable to create zip from directory: ' . $e->getMessage());
        }
    }

    /**
     *
     * @param type $file
     * @return boolean - true on success, false on failure<br />
     * <br />Create a zip file from a file
     */
    public function ZipFromFile($file) {

        try {

            if (is_file($file)) {

                $this->zip->addFile($file);

                $this->zip->close();

                return true;
            }
            else
                return false;
        } catch (Exception $e) {

            trigger_error('Unable to create zip from file: ' . $e->getMessage());
        }
    }

}