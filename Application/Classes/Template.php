<?php

class Template extends Router {

    private
            $title;

    /**
     *
     * @param string $template - template to render
     * @param array $params - The parameters to pass to a controller
     */

    public function __construct() {
        ;
    }

    public function Render($template, $params = null) {

        $error = null;

        $this->title = (!empty($params['PageTitle']) ? $params['PageTitle'] : $this->Router->pageTitle );

        extract($params);

        ob_start();

        $templateParams = explode(':', $template);

        if (strtolower($templateParams[0]) == 'bundle') {
            
            $path = BUNDLES_FOLDER . $templateParams[1] . '/Templates/';

            $templateURL = $this->stripDoubleSlashes(BUNDLES_FOLDER . $templateParams[1] . '/Templates/ControllerViews/' . $templateParams[2]);

            if (is_file($templateURL)) {

                if (is_file($path . 'Header.html.php')) require_once $path . 'Header.html.php';
                require_once $templateURL;
                if (is_file($path . 'Footer.html.php')) require_once $path . 'Footer.html.php';
            }
            else
                $error = 'TNF';
        }
        else {

            $templateURL = $this->stripDoubleSlashes(TEMPLATES_FOLDER . $templateParams[0] . '/' . $templateParams[1] . '/' . $templateParams[2]);

            if (is_file($templateURL)) {

                $type = '_Public/';
                if (strpos($template, '_Private') > -1) $type = '_Private/';
                else if (strpos($template, '_Protected') > -1) $type = '_Protected/';

                require_once TEMPLATES_FOLDER . 'Header.html.php';
                require_once TEMPLATES_FOLDER . $type . 'Header.html.php';
                require_once $templateURL;
                require_once TEMPLATES_FOLDER . $type . 'Footer.html.php';
                require_once TEMPLATES_FOLDER . 'Footer.html.php';
            }
            else
                $error = 'TNF';
        }

        if ($error == 'TNF')
            $this->templateNotFound($templateURL);

        $html = ob_get_clean();

        if(ENABLE_HTML_VALIDATION && !empty($html)){
         
            $validation = new ValidationEngine();
            $validation->validateHTML ($html);
        }

        echo $html;

        unset($html);

        exit;
    }

    /**
     *
     * @param type $template - if template not found, render template not found error page
     */
    private function templateNotFound($template){

        $params['Backtrace'] = debug_backtrace();

        $params['Error'] = array(

          'Template' => $template
        );

        require_once BUNDLES_FOLDER . '/Errors/Templates/Header.html.php';
        require_once BUNDLES_FOLDER . '/Errors/Templates/ControllerViews/Template_Not_Found.html.php';
        require_once BUNDLES_FOLDER . '/Errors/Templates/Footer.html.php';

    }

    /**
     *
     * @param type $template
     * @param type $params
     * @return string $html - returns the html of the page rendered for further process or output.
     */
    public function RenderTemplate($template, $params = null) {

        $this->title = (!empty($params['PageTitle']) ? $params['PageTitle'] : $this->Router->pageTitle );

        if(is_array($params))
            extract($params);

        $templateParams = explode(':', $template);

        $dirRoot = ($templateParams[0] == 'Bundle' ? BUNDLES_FOLDER . $templateParams[1] . '/Templates/ControllerViews' : TEMPLATES_FOLDER);

        $templateURL = $dirRoot . '/' . $templateParams[2];

        $templateURL = $this->stripDoubleSlashes($templateURL);

        if(!is_file($templateURL)){

            $this->templateNotFound ($templateURL);

            exit;

        }

        ob_start();

        require_once $templateURL;

        $html = ob_get_clean();

        return $html;
    }

    /**
     *
     * @param string $css
     * @param type $params
     * Generates a css html tag
     */
    public function includeCSS($css, $params = null) {

        $css = '<link rel="stylesheet" type="text/css" href="' . CSS_FOLDER . $css . '" ' . $params . ' />';

        echo $css;
    }

    /**
     *
     * @param string $js
     * @param type $params
     * Generates a JS html tag
     */
    public function includeJS($js, $params = null) {

        $js = '<script type="text/javascript" src="' . JS_FOLDER . $js . '" ' . $params . '></script>';

        echo $js;
    }

    /**
     *
     * @param string $image
     * @param type $params
     * Generates an img html tag
     */
    public function includeImage($image, $params = null) {

        $image = '<img src="' . IMAGES_FOLDER . $image . '" ' . $params . ' />';

        echo $image;
    }

    /**
     *
     * @param type $asset
     * @return string Returns asset url.
     *
     */
    public function Asset($asset) {

        $chunks = explode('.', $asset);

        $identifier = end($chunks);

         if ($identifier == 'js')
                $asset = JS_FOLDER . '/' . $asset;

            else if ($identifier == 'css')
                $asset = CSS_FOLDER . 'Assets/CSS/' . $asset;

            else if ($identifier == 'png' || $identifier == 'bmp' || $identifier == 'jpg' || $identifier == 'jpeg' || $identifier == 'gif' || $identifier == 'tiff')
                $asset = IMAGES_FOLDER . $asset;
            else
                $asset = SOURCE_FOLDER . 'Assets/' . $asset;

        return $asset;
    }

    /**
     *
     * @param type $asset
     * @param type $params
     * Returns tag depending whether the asset is an image a css or a js file.
     */
    public function setAsset($asset, $params = null) {

        $chunks = explode('.', $asset);

        $identifier = end($chunks);

        if ($identifier != false) {

            if ($identifier == 'js')
                $this->includeJS($asset, $params);

            else if ($identifier == 'css')
                $this->includeCSS($asset, $params);

            else if ($identifier == 'png' || $identifier == 'bmp' || $identifier == 'jpg' || $identifier == 'jpeg' || $identifier == 'gif' || $identifier == 'tiff')
                $this->includeImage($asset, $params);
            else
                $this->Asset($asset);
        }
        else
            echo 'Unable to include asset, file extension missing.';
    }

    /**
     *
     * @param type $array - the flash message to set
     * Set a flash message for the immidiate next template to be rendered, after displaying the message is deleted.
     */
    protected function setFlash($message) {

        if(is_array($message)){
            foreach ($message as $me) {

                $_SESSION['FlashMessages'][] = $me;
            }
        }
        else
            $_SESSION['FlashMessages'][] = $message;
    }

    /**
     *
     * @param type $array - The error message to display
     * Sets an Error message for the immidiate next template to be rendered, after displaying message is deleted.
     */
    protected function setError($message) {

        if(is_array($message)){
            foreach ($message as $error) {

                $_SESSION['Errors'][] = $error;
            }
        }
        else
            $_SESSION['Errors'][] = $message;
    }

    /**
     *
     * @param type $array
     * @return type
     * Usage IfExistsElse(array('ifissetthis','elsethis', 'Optional Condition here e.g > < == >= <=')), if option not set then will be used if only exists;
     * Example IfExistsElse(array($variable1,$variable2,'==');
     *
     */
    public function IfExistsElse($if, $else, $operator = null) {

        $then = $if;

        if (isset($operator)) {

            if (strpos($operator, 'callfunc:') == 0) {

                $funcName = explode(':', $operator);

                if (isset($funcName[2]))
                    $then = $funcName[2];

                $value = (call_user_func($funcName[1], $if) ? $then : $else);
            }
            else {

                switch ($operator) {

                    case '<':
                        $value = (($if < $else) ? $then : $else );
                        break;

                    case '>':
                        $value = (($if > $else) ? $then : $else );
                        break;

                    case '==':
                        $value = (($if == $else) ? $then : $else );
                        break;

                    case '<=':
                        $value = (($if <= $else) ? $then : $else );
                        break;

                    case '>=':
                        $value = (($if >= $else) ? $then : $else );
                        break;
                }
            }
        }
        else
            $value = (($if) ? $then : $else );

        return $value;
    }

    /**
     * Display Errors set by setErrors
     */
    public function Errors() {

        if (!empty($_SESSION['Errors'])) {

            foreach ($_SESSION['Errors'] as $error) {

                echo '<div class="error alert">' . $error . '</div>';
            }

            unset($_SESSION['Errors']);
        }
    }

    /**
     * Flash all Flash messages and Errors
     */
    public function FlashAll(){

        $this->Errors();

        $this->Flashes();
    }

    /**
     * flash all messages set by setFlash
     */
    public function Flashes() {

        if (!empty($_SESSION['FlashMessages'])) {

            foreach ($_SESSION['FlashMessages'] as $message) {

                echo '<div class="message alert">'  . $message . '</div>';
            }

            unset($_SESSION['FlashMessages']);
        }
    }
    
    public function stripDoubleSlashes($string){
        
        return str_replace('//', '/', $string);
    }

}