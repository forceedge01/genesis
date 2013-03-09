<?php

class Console{

    public function unknownOption(){

        echo 'Unknown option!';

        $this->showAllOptions();

        $message = 'Enter option: ';

        $option = $this->readUser($message);

        return $option;
    }

    public function readUser($message = null){

        if(!empty($message))
            echo $message;

        $handle = fopen('php://stdin', 'r');

        $line = trim(fgets($handle));

        return $line;
    }

    public function writeUser($message){

        echo $message;
    }

    public function showAllOptions(){

        $options = array(
           'bundle:create',
           'bundle:delete'
        );

        $this->linebreak(2);

        foreach($options as $option){

            echo $option;
            $this->linebreak(1);
        }

        $this->linebreak(2);
    }

    protected function removeDirectory($directory){

        try{

            if(is_dir($directory)){

                $files = scandir($directory);

                foreach($files as $file)
                {
                    if(($file != '.' && $file != '..')){

                        $absolutePath = $directory . '/' . $file;

                        if(is_dir($absolutePath))
                        {
                            echo 'Entring direcotry';
                            $this->linebreak(1);
                            $this->removeDirectory($absolutePath);
                        }
                        else
                        {
                            echo 'deleting file: '. $absolutePath;
                            $this->linebreak(1);
                            unlink($absolutePath);
                        }
                    }
                }

                if(rmdir($directory))
                    return true;
                else
                    return false;

            }
            else
                return false;

        }
        catch(Exception $e){

            echo $e->getMessage();

            return false;
        }
    }

    public function linebreak($val){

        for($i = 0; $i < $val; $i++)
            echo chr(10) . chr(13);
    }

}