<?php

namespace Application\Core;



class Template extends Router {

    private
            $title,
            $bundle,
            $html;

    public static
            $cssFiles = array(),
            $jsFiles = array();

    /**
     *
     * @param string $template - template to render
     * @param array $params - The parameters to pass to a controller
     */

    public function __construct() {
        ;
    }

    private function GetTemplate($template){

        $templateParams = explode(':', $template);

        if ($templateParams[0] != null) {

            return array(
                'template' => $this->refactorUrl($this->stripDoubleSlashes(BUNDLES_FOLDER . $templateParams[0] . BUNDLE_VIEWS .'ControllerViews/' . $templateParams[1])),
                'path' => $this->refactorUrl(BUNDLES_FOLDER . $templateParams[0] . BUNDLE_VIEWS)
            );
        }
        else{

            return array(
                'template' => $this->refactorUrl($this->stripDoubleSlashes(TEMPLATES_FOLDER . $templateParams[1] )),
                'path' => $this->refactorUrl(TEMPLATES_FOLDER)
            );
        }
    }

    /**
     *
     * @param type $template
     * @param array $params - pass in data to template, set PageTitle in array to set the title of the page
     */
    public function Render($template, $pageTitle, array $params = array()) {

        $this->title = $pageTitle;

        extract($params);
        unset($params);

        $templateUrl = $this->GetTemplate($template);

        ob_start();

        if(is_file($templateUrl['template'])){

            require $templateUrl['path'].BUNDLE_VIEW_HEADER_FILE;
            require $templateUrl['template'];
            require $templateUrl['path'].BUNDLE_VIEW_FOOTER_FILE;
        }
        else
            $this->templateNotFound($templateUrl['template']);

        $this->html = ob_get_clean();

        $this->CheckJsCacheOptions();
        $this->CheckCssCacheOptions();
        $this->CheckHtmlCacheOptions();

        if(\Get::Config('Cache.html.compress.enabled'))
            $this->html = Cache::Compress ($this->html, \Get::Config('Cache.html.compress.level'));

        if(\Get::Config('Errors.enableHtmlValidation') && !empty($this->html))
        {
            $this->GetComponent('ValidationEngine')->validateHTML ($this->html);
        }

        echo $this->html;

        if(\Get::Config('Cache.html.enabled'))
            Cache::WriteCacheFile($this->SetPattern()->GetPattern(), $this->html);

        unset($this->html);
    }

    private function CheckHtmlCacheOptions()
    {
        if(\Get::Config('Cache.html.enabled'))
        {
            $html = $this->html;

            if(\Get::Config('Cache.html.minify'))
                $html = Cache::Minify ($html, 'html');

            if(\Get::Config('Cache.html.compress.enabled'))
                $html = Cache::Compress ($html, \Get::Config('Cache.html.compress.level'));

            $this->html = $html;

            return Cache::WriteCacheFile($this->SetPattern()->GetPattern(), $this->html);
        }
    }

    private function CheckJsCacheOptions()
    {
        if(\Get::Config('Cache.javascript.enabled'))
        {
            $html = $this->html;

            if(\Get::Config('Cache.javascript.unify'))
                $html = Cache::Unify ($html, self::$jsFiles);

            if(\Get::Config('Cache.javascript.minify'))
            {
                foreach(self::$jsFiles as $file)
                    Cache::Minify ($file, 'javascript');
            }

            $this->html = $html;

            return true;
        }
    }

    private function ReplaceJsFiles($jsFiles)
    {
        for($index = 0; $index < count($jsFiles); $index++ )
            $this->html = str_replace (self::$jsFiles[$index], $jsFiles[$index], $this->html);

        return true;
    }

    private function CheckCssCacheOptions()
    {
        if(\Get::Config('Cache.css.enabled'))
        {
            $html = $this->html;

            if(\Get::Config('Cache.css.unify'))
                $html = Cache::Unify ($html, self::$cssFiles);

            if(\Get::Config('Cache.css.minify'))
                foreach(self::$cssFiles as $file)
                    Cache::Minify ($file, 'css');

            $this->html = $html;

            return true;
        }
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

        require_once APPLICATION_RESOURCES_FOLDER . 'Views/Header.html.php';
        require_once APPLICATION_RESOURCES_FOLDER . '/Views/Errors/Template_Not_Found.html.php';
        require_once APPLICATION_RESOURCES_FOLDER . '/Views/Footer.html.php';

    }

    /**
     *
     * @param string $template
     * @param mixed $params
     * @param boolean $extract Extract variables and unset the params array - defaults to true
     * @return string $html - returns the html of the page rendered for further process or output.
     */
    public function RenderTemplate($template, $params = array(), $extract = true)
    {
        if($extract)
        {
            extract($params);
            unset($params);
        }

        $template = $this->GetTemplate($template);

        if(!is_file($template['template'])){

            $this->templateNotFound ($template['template']);

            exit;
        }

        ob_start();

        require $template['template'];

        $html = ob_get_clean();

        return $html;
    }

    /**
     *
     * @param string $template
     * @param mixed $params
     * @return html Will include a template and pass variables without extracting them, prefereable use within templates to include other templates
     */
    public function IncludeTemplate($template, $params)
    {
        return $this->RenderTemplate($template, $params, false);
    }

    /**
     *
     * @param string $css
     * @param type $params
     * Generates a css html tag
     */
    public function includeCSS($css, $params = null) {

        $source = null;

        if($this->associateAssetBundle($css))
            $source =  $this->bundle[0] . 'CSS/' . $this->bundle[1];
        else
            $source = CSS_FOLDER . $css;

        self::$cssFiles[] = $source;

        echo "<link rel='stylesheet' type='text/css' href='{$source}' {$params}/>";
    }

    /**
     *
     * @param string $js
     * @param type $params
     * Generates a JS html tag
     */
    public function includeJS($js, $params = null) {

        $source = null;

        if($this->associateAssetBundle($js))
            $source =  $this->bundle[0] . 'JS/' . $this->bundle[1];
        else
            $source = JS_FOLDER . $js;

        self::$jsFiles[] = $source;

        echo "<script type='text/javascript' src='{$source}' {$params}></script>";
    }

    /**
     *
     * @param string $image
     * @param type $params
     * Generates an img html tag
     */
    public function includeImage($image, $params = null) {

        if($this->associateAssetBundle($image))
            $image = '<img src="' . $this->bundle[0] . 'Images/' . $this->bundle[1] . '" ' . $params . ' />';
        else
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
            return JS_FOLDER . '/' . $asset;

        else if ($identifier == 'css')
            return CSS_FOLDER . 'Assets/CSS/' . $asset;

        else if ($identifier == 'png' || $identifier == 'bmp' || $identifier == 'jpg' || $identifier == 'jpeg' || $identifier == 'gif' || $identifier == 'tiff')
            return IMAGES_FOLDER . $asset;
        else
            return SOURCE_FOLDER . 'Assets/' . $asset;
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

        if(!empty($message))
        {
            if(is_array($message))
            {
                foreach ($message as $me)
                {
                    $_SESSION['FlashMessages'][] = $me;
                }
            }
            else
            {
                $_SESSION['FlashMessages'][] = $message;
            }
        }

        return $this;
    }

    /**
     *
     * @param type $array - The error message to display
     * Sets an Error message for the immidiate next template to be rendered, after displaying message is deleted.
     */
    protected function setError($message) {

        if($message)
        {
            if(is_array($message))
            {
                foreach ($message as $error)
                {
                    $_SESSION['Errors'][] = $error;
                }
            }
            else
            {
                $_SESSION['Errors'][] = $message;
            }
        }

        return $this;
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

        if (!empty($_SESSION['Errors']))
        {
            foreach ($_SESSION['Errors'] as $error)
            {
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

        if (!empty($_SESSION['FlashMessages']))
        {
            foreach ($_SESSION['FlashMessages'] as $message)
            {
                echo '<div class="message alert">'  . $message . '</div>';
            }

            unset($_SESSION['FlashMessages']);
        }
    }

    /**
     *
     * @param type $params
     * @return string A styled string for elements
     */
    public function applyStyle($params = array()){

        $style = ' style="';

        foreach($params as $key => $param){

            $style .= "$key: $param; ";
        }
        $style .= '"';

        return $style;
    }

    private function associateAssetBundle($param){

        $chunks = explode(':', $param);

        if(isset($chunks[1])){

            $this->bundle[0] = ASSETS_FOLDER . 'Bundles/' . $chunks[0] . '/';
            $this->bundle[1] = $chunks[1];

            return true;
        }
        else
            return false;
    }

    public function Cycle($index, $even = 'even', $odd = 'odd'){

        if($this->IsEven($index)) return $even;
        return $odd;
    }

    public function GenerateNumberOptions($start, $end, $leap = 1, $selected = null){

        if($start > $end)
            for($i = $start; $start >= $end; $i -= $leap){

                $options .= $this->GenerateOption($i, $i, $selected);
        }
        else if($start < $end)
            for($i = $start; $start <= $end; $i += $leap){

                $options .= $this->GenerateOption($i, $i, $selected);
            }

        return $options;
    }

    public function GenerateOption($value, $label, $selected = null){

        $option .= "<option value='$value' ";
        if($this->Variable($selected)->Equals($value)) $option .= 'selected="selected" ';
        $option .= " >$label</option>";

        return $option;
    }

    /**
     *
     * @param type $value
     * @return \Application\Core\Template|boolean
     * @desc Checks if the value is even
     */
    public function IsEven($value){

        if($value % 2 == 0) return $this;
        return false;
    }

    /**
     *
     * @param type $value
     * @return \Application\Core\Template|boolean
     * @desc Checks if the value is odd
     */
    public function IsOdd($value){

        if($value % 2 != 0) return $this;
        return false;
    }
}