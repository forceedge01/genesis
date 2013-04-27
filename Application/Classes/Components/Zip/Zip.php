<?php

namespace Application\Components\Zip;



class Zip extends \ZipArchive {

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
        } catch (Exception $e) {

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

            if (!empty($exceptionEntry)) {

                for ($i = 0; $i < $this->zip->numFiles; $i++) {

                    foreach ($exceptionEntry as $exception) {

                        if ($this->zip->getNameIndex($i) != $exception)
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

    function zip_info_generator($zip = null) {

        if(empty($zip))
            $zip = $this->zip;
        else
            $zip = zip_open($zip);

        $folder_count = 0;
        $file_count = 0;
        $unzipped_size = 0;

        $ext_array = array();
        $ext_count = array();

        if ($zip) {

            while ($zip_entry = zip_read($zip)) {

                if (strrpos(zip_entry_name($zip_entry), '/')+1 == strlen(zip_entry_name($zip_entry))) {

                    $folder_count++;

                } else {

                    $file_count++;

                }

                $path_parts = pathinfo(zip_entry_name($zip_entry));
                $ext = strtolower(trim(isset($path_parts['extension']) ? $path_parts['extension'] : ''));

                if ($ext != '') {

                    $ext_count[$ext]['count'] = isset($ext_count[$ext]['count']) ? $ext_count[$ext]['count'] : 0;
                    $ext_count[$ext]['count']++;

                }

                $unzipped_size = $unzipped_size + zip_entry_filesize($zip_entry);
            }

        }

        $zipped_size = $this->get_file_size_unit(filesize(TEST_CANDIDATE_DIR . STREETTEAM_ZIP_NAME));

        $unzipped_size = $this->get_file_size_unit($unzipped_size);

        $zip_info = array(

            "folders" => $folder_count,
            "files" => $file_count,
            "total" => $folder_count+$file_count,
            "zipped_size" => $zipped_size,
            "unzipped_size" => $unzipped_size,
            "file_types" => $ext_count
        );

        zip_close($zip);

        return $zip_info;
    }

    function get_file_size_unit($file_size) {

        if ($file_size / 1024 < 1) {

            return $file_size . "Bytes";

        }
        else if ($file_size / 1024 >= 1 && $file_size / (1024 * 1024) < 1)
        {

            return ($file_size / 1024) . "KB";

        }
        else
        {

            return $file_size / (1024 * 1024) . "MB";
        }
    }

}