<?php

namespace Application\Components\TemplateHandler;


use Application\Core\Interfaces\TemplateInterface;
use Application\Core\Lib\AppMethods;

class TemplateHandler extends AppMethods implements TemplateInterface {

    private
            $title,
            $html,
            $header,
            $footer,
            $divs,
            $classCalledFrom;

    public static
            $cssFiles = array(),
            $jsFiles = array();

    public $Session, $Server;

    /**
     *
     * @param string $template - template to render
     * @param array $params - The parameters to pass to a controller
     */
    private function GetView($templatePath){

        list($bundle, $controller, $template) = explode(':', $templatePath);

        if ($bundle) {

            return array(
                'template' =>
                        \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') .
                        $this->GetBundleFromName($bundle) .
                        \Get::Config('APPDIRS.BUNDLES.VIEWS') . "$controller/$template"
                 ,
                'path' =>
                        \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER') .
                        $bundle .
                        \Get::Config('APPDIRS.BUNDLES.VIEWS').
                        "$controller/"
            );
        }
        else
        {
            return array(
                'template' => \Get::Config('APPDIRS.TEMPLATING.TEMPLATES_FOLDER') . "$controller/$template",
                'path' => \Get::Config('APPDIRS.TEMPLATING.TEMPLATES_FOLDER') . "$controller/"
            );
        }
    }

    /**
     *
     * @param string $template bundle:template
     * @param string $pageTitle The page title to set
     * @param array $params - pass in data to template
     */
    public function Render($template, $pageTitle, array $params = array()) {

        $this->title = $pageTitle;

        $this
            ->ExportGlobalVars()
            // Something wrong with this call, simplify this component anyway
            ->ProcessOutput($this->GetView($template), $params)
            ->CheckJsCacheOptions()
            ->CheckCssCacheOptions()
            ->CheckHtmlCacheOptions();

        if(\Get::Config('Cache.html.compress.enabled'))
            $this->html = Cache::Compress ($this->html, \Get::Config('Cache.html.compress.level'));

        if(\Get::Config('Errors.enableHtmlValidation') && !empty($this->html))
        {
            $this->GetComponent('ValidationEngine')->validateHTML ($this->html);
        }

        $response = $this->getComponent('Response');
        $response->setContent($this->GetOutput());

        if(\Get::Config('Cache.html.enabled'))
            Cache::WriteCacheFile($this->SetPattern()->GetPattern(), $this->html);

        return $response;
    }

    private function ExportGlobalVars()
    {
//        $this->server = $this->ArrayToObject($_SERVER);
//        $this->session = $this->ArrayToObject($_SESSION);

        return $this;
    }

    public function Render404Response()
    {
        $this->ForwardToController('404');
    }

    public function Render500Response()
    {
        $this->ForwardToController('500');
    }

    private function ProcessOutput($templateUrl, $params)
    {
        extract($params, EXTR_OVERWRITE);
        ob_start();

        if(is_file($templateUrl['template']))
        {
            if($this->header)
                $this->GetBundleHeader ();

            require $templateUrl['template'];

            if($this->footer)
                $this->GetBundleFooter ();
        }
        else
            $this->ViewNotFound($templateUrl['template']);

        $this->EndOutput();

        return $this;
    }

    public function EndOutput()
    {
        $this->html = ob_get_clean();
    }

    public function GetOutput()
    {
        return $this->html;
    }

    public function IncludeHeaderAndFooter($class)
    {
        $this->IncludeHeader($class);
        $this->IncludeFooter($class);

        return $this;
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

        return $this;
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
        }

        return $this;
    }

    private function ReplaceJsFiles($jsFiles)
    {
        for($index = 0; $index < count($jsFiles); $index++ )
            $this->html = str_replace (self::$jsFiles[$index], $jsFiles[$index], $this->html);

        return $this;
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
        }

        return $this;
    }

    /**
     *
     * @param type $template - if template not found, render template not found error page
     */
    private function ViewNotFound($template){

        $params['Error'] = array(

          'Template' => $this->RefactorUrl($template),
          'Backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        );

        $this->ForwardToController('Template_Not_Found', $params);
    }

    public function IncludeHeader($class)
    {
        $this->classCalledFrom = $class;
        $this->header = true;

        return $this;
    }

    protected function GetBundleHeader($bundle = null)
    {
        if(! $bundle)
            $bundle = $this->GetBundleFromName($this->GetClassFromNameSpacedController ($this->classCalledFrom));

        $path = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER').
                $bundle.
                '/'.
                \Get::Config('APPDIRS.BUNDLES.VIEWS').
                \Get::Config('APPDIRS.BUNDLES.BUNDLE_VIEW_HEADER_FILE');

        if(is_file($path))
        {
            require_once $path;
        }
        else
        {
            $this->ViewNotFound ($path);
        }

        return $this;
    }

    public function IncludeFooter($class)
    {
        $this->classCalledFrom = $class;
        $this->footer = true;

        return $this;
    }

    protected function GetBundleFooter($bundle = null)
    {
        if(! $bundle)
            $bundle = $this->GetBundleFromName($this->GetClassFromNameSpacedController ($this->classCalledFrom));

        $path = \Get::Config('APPDIRS.BUNDLES.BASE_FOLDER').
                $bundle.
                '/'.
                \Get::Config('APPDIRS.BUNDLES.VIEWS').
                \Get::Config('APPDIRS.BUNDLES.BUNDLE_VIEW_FOOTER_FILE');

        if(is_file($path))
            require_once $path;
        else
            $this->ViewNotFound ($path);

        return $this;
    }

    /**
     *
     * @param string $template
     * @param mixed $params
     * @param boolean $extract Extract variables and unset the params array - defaults to true
     * @return string $html - returns the html of the page rendered for further process or output.
     */
    public function RenderView($template, $params = array(), $extract = true)
    {
        if($extract)
        {
            extract($params);
            unset($params);
        }

        $template = $this->GetView($template);

        if(! is_file($template['template'])){

            $this->ViewNotFound ($template['template']);
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
    public function IncludeView($template, array $params = array())
    {
        return $this->RenderView($template, $params, false);
    }

    /**
     *
     * @param string $css
     * @param type $params
     * Generates a css html tag
     */
    public function IncludeCSS($css, $params = null) {

        self::$cssFiles[] = $css;

        echo "<link rel='stylesheet' type='text/css' href='{$css}' {$params}/>";
    }

    /**
     *
     * @param string $js
     * @param type $params
     * Generates a JS html tag
     */
    public function IncludeJS($js, $params = null) {

        self::$jsFiles[] = $js;

        echo "<script type='text/javascript' src='{$js}' {$params}></script>";
    }

    /**
     *
     * @param string $image
     * @param type $params
     * Generates an img html tag
     */
    public function IncludeImage($image, $params = null) {

        echo '<img src="' . $image . '" ' . $params . ' />';
    }

    /**
     *
     * @param type $asset
     * @return string Returns asset url.
     * @deprecated since version 1.0.0
     *
     */
    public function Asset($asset) {

        $chunks = explode('.', $asset);

        $identifier = end($chunks);

         if ($identifier == 'js')
            return \Get::Config('APPDIRS.TEMPLATING.JS_FOLDER') . $asset;

        else if ($identifier == 'css')
            return \Get::Config('APPDIRS.TEMPLATING.CSS_FOLDER')  . $asset;

        else if ($identifier == 'png' || $identifier == 'bmp' || $identifier == 'jpg' || $identifier == 'jpeg' || $identifier == 'gif' || $identifier == 'tiff')
            return \Get::Config('APPDIRS.TEMPLATING.IMAGES_FOLDER') . $asset;
        else
            return \Get::Config('APPDIRS.TEMPLATING.ASSETS_FOLDER') . $asset;
    }

    public function Path($route, array $routeVars = array())
    {
        return $this->getComponent('Router')->GetRoute($route, $routeVars);
    }

    /**
     *
     * @param type $asset
     * @param type $params
     * Returns tag depending whether the asset is an image, css or a js file.
     */
    public function SetAsset($asset, $params = null) {

        $extension = pathinfo($asset, PATHINFO_EXTENSION);
        $asset = $this->associateAssetBundle($asset);

        if ($extension) {

            if ($extension == 'js')
                $this->includeJS($asset, $params);
            else if ($extension == 'css')
                $this->includeCSS($asset, $params);
            else if ($extension == 'png' ||
                    $extension == 'bmp' ||
                    $extension == 'jpg' ||
                    $extension == 'jpeg' ||
                    $extension == 'gif' ||
                    $extension == 'tiff')
                $this->includeImage($asset, $params);
            else
                $this->Asset($asset);
        }
        else
            echo 'Unable to include asset, file extension missing.';
    }

    /**
     * @param array $exclusions names of files to exclude
     */
    public function IncludeBundleAssets($bundle, array $exclusions = array(), $css = true, $js = true)
    {
        $path = \Get::Config('APPDIRS.BUNDLES.ASSETS_FOLDER');

        if($css)
        {
            $this->IncludeAssetDir($bundle, 'css', $path.$bundle.'/CSS', $exclusions);
        }

        if($js)
        {
            $this->IncludeAssetDir($bundle, 'js', $path.$bundle.'/JS', $exclusions);
        }
    }

    private function IncludeAssetDir($bundle, $assetType, $dir, $exclusions = array(), $append = null)
    {
        if(! is_dir($dir))
        {
            Debugger::ThrowStaticError('Unable to include dir ' . $dir . ' in ' . __METHOD__ . ' requested by ' . get_called_class(), __FILE__, __LINE__);
        }

        $assets = scandir($dir);

        foreach($assets as $asset)
        {
            if($asset != '.' and $asset != '..' and ! array_search($asset, $exclusions))
            {
                switch($assetType)
                {
                    case 'css':
                    {
                        $method = 'includeCSS';
                        $folder = 'CSS/';

                        break;
                    }

                    case 'js':
                    {
                        $method = 'includeCSS';
                        $folder = 'JS/';

                        break;
                    }
                }

                $url = $this->associateAssetBundle($bundle);

                if(is_dir($dir.'/'.$asset))
                {
                    $this->IncludeAssetDir ($bundle, $assetType, $dir.'/'.$asset, array(), str_replace(ROOT.'Public/Assets/Bundles/'.$bundle.'/'.$folder, '', $dir.'/'.$asset));
                }
                else
                {
                    if($append)
                        $append .= '/';

                    $this->$method ($url.$folder.$append.$asset);
                }
            }
        }
    }

    /**
     *
     * @param string $bundle
     */
    public function IncludeBundleCssAssets($bundle, $exclusions = array())
    {
        $this->IncludeBundleAssets($bundle, $exclusions, true, false);
    }

    /**
     *
     * @param string $bundle
     */
    public function IncludeBundleJsAssets($bundle, $exclusions = array())
    {
        $this->IncludeBundleAssets($bundle, $exclusions, false, true);
    }

    /**
     *
     * @param type $array - the flash message to set
     * Set a flash message for the immidiate next template to be rendered, after displaying the message is deleted.
     */
    public function SetFlash($message) {

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
    public function SetError($message) {

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
     * Display Errors set by SetErrors
     */
    public function Errors() {

        if (!empty($_SESSION['Errors']))
        {
            foreach ($_SESSION['Errors'] as $error)
            {
                echo '<div class="error alert">' . $error . '</div>';
            }

            $this->ClearErrors();
        }
    }

    /**
     * flash all messages set by SetFlash
     */
    public function Flashes() {

        if (!empty($_SESSION['FlashMessages']))
        {
            foreach ($_SESSION['FlashMessages'] as $message)
            {
                echo '<div class="message alert">'  . $message . '</div>';
            }

            $this->ClearFlashes();
        }
    }

    public function ClearFlashes()
    {
        unset($_SESSION['FlashMessages']);

        return $this;
    }

    public function ClearErrors()
    {
        unset($_SESSION['Errors']);

        return $this;
    }

    public function GetErrors()
    {
        return $_SESSION['Errors'];
    }

    public function GetFlashes()
    {
        return $_SESSION['FlashMessages'];
    }

    /**
     * Flash all Flash messages and Errors
     */
    public function FlashAll(){

        $this->Errors();

        $this->Flashes();
    }

    /**
     *
     * @param type $params
     * @return string A styled string for elements
     */
    public function ApplyStyle($params = array()){

        $style = ' style="';

        foreach($params as $key => $param){

            $style .= "$key: $param; ";
        }
        $style .= '"';

        return $style;
    }

    private function associateAssetBundle($param)
    {
        $asset = null;

        if(strpos($param, ':') !== false)
        {
            list($bundle, $asset) = explode(':', $param);

            if($bundle)
                $path = \Get::Config('APPDIRS.TEMPLATING.ASSETS_FOLDER') . 'Bundles/' . $bundle .'/'. $asset;
        }

        $path = \Get::Config('APPDIRS.TEMPLATING.ASSETS_FOLDER') . 'Common/' .$asset;

        return $path;
    }

    /**
     *
     * @param type $index
     * @param type $even
     * @param type $odd
     * @return type
     */
    public function Cycle($index, $even = 'even', $odd = 'odd'){

        if($this->IsEven($index)) return $even;
        return $odd;
    }

    /**
     *
     * @param type $start
     * @param type $end
     * @param type $leap
     * @param type $selected
     * @return type
     */
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

    /**
     *
     * @param type $value
     * @param type $label
     * @param type $selected
     * @return type
     */
    public function GenerateOption($value, $label, $selected = null){

        $option .= "<option value='$value' ";
        if($selected === $value) $option .= 'selected="selected" ';
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

    /**
     *
     * @param type $label
     * @param type $for optional
     * @param type $params optional
     * @return type
     */
    protected function Label($label, $for = null, $params = null)
    {
        if(! $for)
            $for = $label;

        return "<label for='{$for}' {$params}>{$label}</label>";
    }

    /**
     *
     * @param type $label
     * @param type $decider
     * @param type $for
     * @param type $params
     * @return type
     */
    protected function IfLabel($label, $decider = null, $for = null, $params = null)
    {
        if(! $decider)
            return false;

        if(! $for)
            $for = $label;

        return "<label for='{$for}' {$params}>{$label}</label>";
    }

    /**
     *
     * @param type $name
     * @param type $value optional
     * @param type $element default text
     * @param type $class optional
     * @param type $id optional
     * @return type
     */
    protected function Widget($name, $value = null, $element = 'text', array $params = array())
    {
        if(! isset($params['id']))
            $params['id'] = $name;

        $htmlgen = $this->GetComponent('HTMLGenerator');

        $el = array(
            'type' => $element,
            'value' => $value,
            'name' => $name,
        );

        return $htmlgen->generateInput(array_merge($el , $params));
    }

    /**
     *
     * @param type $name
     * @param type $value
     * @param type $element
     * @param type $class
     * @param type $id
     * @param type $decider
     * @return type
     */
    protected function IfWidget($name, $value = null, $element = 'text', array $params = array(), $decider = null)
    {
        if($decider === null)
            $decider = $value;

        if($decider)
            return $this->Widget($name, $value, $element, $params);
    }

    /**
     *
     * @param type $class
     * @param type $id
     * @return type
     */
    protected function Div($class = null, $id = null)
    {
        $this->divs += 1;

        if($class)
            $class = "class='$class'";

        if($id)
            $id = "id='$id'";

        return "<div $class $id>";
    }

    /**
     *
     * @param type $number
     * @return string
     */
    protected function EndDiv($number = 1)
    {
        $this->divs -= $number;
        $divs = null;

        for($i = $number; $i > 0; $i--)
        {
            $divs .= '</div>';
        }

        return $divs;
    }

    /**
     *
     * @return string
     */
    protected function EndDivs()
    {
        $divs = null;

        for($i = $this->divs; $i > 0; $i--)
        {
            $divs .= '</div>';
        }

        $this->divs = 0;

        return $divs;
    }

    /**
     *
     * @param type $name
     * @param type $value
     * @param type $element
     * @param type $class
     * @param type $id
     * @return type
     */
    protected function LabelAndWidget($name, $value = null, $element = 'text', $class = null, $id = null)
    {
        if(! $id)
            $id = $name;

        $htmlgen = $this->GetComponent('HTMLGenerator');

        $el = array(
            'type' => $element,
            'value' => $value,
            'name' => $name,
            'class' => $class,
            'id' => $id,
            'label' => $this->Filter($value, 'FirstToUpper'),
            'for' => $id
        );

        return "<label for='{$id}' {$class}>{$this->Filter($value, 'FirstToUpper')}</label>" . $htmlgen->generateInput($el);
    }

    /**
     *
     * @param type $variable
     * @param type $filter
     * @return type
     */
    protected function Filter($variable, $filter)
    {
        switch($filter)
        {
            case 'FirstToUpper':
            {
                return $this->Variable($variable)->FirstToUpper()->GetVariableResult();
                break;
            }
        }
    }

    /**
     *
     * @param type $message
     * @return type
     */
    protected function Error($message, $class = null , $id = null)
    {
        if($id)
            $id = "id='$id'";

        return "<div class='error $class' $id>$message</div>";
    }
}