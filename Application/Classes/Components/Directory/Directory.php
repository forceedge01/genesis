<?php

class Dir extends Debugger {

    public
            $directory,
            $verbose;

    /**
     *
     * @param string $directory - target directory
     * @return boolean - true on success, false on failure<br />
     * <br />Removes a directory completely even if not empty.
     */
    public function removeDirectory($directory) {

        try {

            if (is_dir($directory)) {

                $files = scandir($directory);

                foreach ($files as $file) {
                    if (($file != '.' && $file != '..')) {

                        $absolutePath = $directory . '/' . $file;

                        if (is_dir($absolutePath)) {
                            if ($this->verbose)
                                echo 'Entring direcotry';
                            $this->removeDirectory($absolutePath);
                        }
                        else {
                            if ($this->verbose)
                                echo 'deleting file: ' . $absolutePath;
                            unlink($absolutePath);
                        }
                    }
                }

                if (rmdir($directory))
                    return true;
                else
                    return false;
            }
            else
                return false;
        } catch (Exception $e) {

            echo $e->getMessage();

            return false;
        }
    }

    /**
     *
     * @param string $directory - target directory
     * @return boolean - true on success, false on failure<br />
     * <br />Delete all contents of a directory
     */
    public function cleanDirectory($directory) {

        $this->directory = $directory;

        if ($this->removeDirectory($directory))
            if (mkdir($this->directory))
                return true;

        return false;
    }

    /**
     *
     * @param type $directory - target directory
     * @return boolean - true on success, false on failure<br />
     * <br />create a new directory
     */
    public function createDirectory($directory) {

        if (mkdir($directory))
            return true;

        return false;
    }

    /**
     *
     * @param type $file - file name
     * @param type $content - content to insert into file
     * @return boolean - true on success, false on failure<br />
     * <br />create a new file or overwrite an existing file
     */
    public function createFile($file, $content = null) {

        $handle = fopen($file, 'w+');

        if(!$handle){

            return false;
        }
        else{

            Ïfputs($handle, $content);

            fclose($handle);

            return true;
        }
    }

    /**
     *
     * @param type $file - file name
     * @return boolean - true on success, false on failure<br />
     * <br />Truncate a file to zero length, if file does not exist it will be created.
     */
    public function clearContentsOfFile($file) {

         $handle = fopen($file, 'w');
        if (!$handle) {

            return false;

        }
        else{

            fclose($handle);

            return true;
        }

    }

    /**
     *
     * @param type $file
     * @return boolean - true on success, false on failure<br />
     * <br />Deletes a file
     */
    public function deleteFile($file) {

        if (unlink($file))
            return true;

        return false;
    }

    /**
     *
     * @param type $file - file name
     * @return string Contents of file
     */
    public function readFile($file) {

        $handle = fopen($file);

        $file = fgets($handle);

        fclose();

        return $file;
    }

}