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

        if ($templateParams[0] == 'Bundle') {

            $templateURL = BUNDLES_FOLDER . $templateParams[1] . '/Templates/ControllerViews/' . $templateParams[2];

            $templateURL = str_replace('//', '/', $templateURL);

            if (is_file($templateURL)) {

                if (is_file(BUNDLES_FOLDER . $templateParams[1] . '/Templates/' . 'Header.html.php'))
                    require_once BUNDLES_FOLDER . $templateParams[1] . '/Templates/' . 'Header.html.php';

                require_once $templateURL;

                if (is_file(BUNDLES_FOLDER . $templateParams[1] . '/Templates/' . 'Footer.html.php'))
                    require_once BUNDLES_FOLDER . $templateParams[1] . '/Templates/' . 'Footer.html.php';
            }
            else
                $error = 'TNF';
        }
        else {

            $templateURL = TEMPLATES_FOLDER . $templateParams[0] . '/' . $templateParams[1] . '/' . $templateParams[2];

            $templateURL = str_replace('//', '/', $templateURL);

            if (is_file($templateURL)) {

                $type = '_Public/';

                if (strpos($template, '_Private') > -1)
                    $type = '_Private/';
                else if (strpos($template, '_Protected') > -1)
                    $type = '_Protected/';

                require_once TEMPLATES_FOLDER . 'Header.html.php';

                require_once TEMPLATES_FOLDER . $type . 'Header.html.php';

                require_once $templateURL;

                require_once TEMPLATES_FOLDER . $type . 'Footer.html.php';

                require_once TEMPLATES_FOLDER . 'Footer.html.php';
            }
            else
                $error = 'TNF';
        }

        if ($error == 'TNF') {

            $this->templateNotFound($templateURL);

        }

        $html = ob_get_clean();

        if(ENABLE_HTML_VALIDATION && !empty($html)){

            $validate = new ValidationEngine();
            $validate->validateHTML ($html);
        }

        echo $html;

        unset($html);

        unset($this);

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

        $templateURL = str_replace('//', '/', $templateURL);

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
    protected function setFlash($array) {

        if(is_array($array)){
            foreach ($array as $message) {

                $_SESSION['FlashMessages'][] = $message;
            }
        }
        else
            $_SESSION['FlashMessages'][] = $array;
    }

    /**
     *
     * @param type $array - The error message to display
     * Sets an Error message for the immidiate next template to be rendered, after displaying message is deleted.
     */
    protected function setError($array) {

        if(is_array($array)){
            foreach ($array as $error) {

                $_SESSION['Errors'][] = $error;
            }
        }
        else
            $_SESSION['Errors'][] = $array;
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
    /**
     *
     * @param type $items
     * @param type $contentDescriptiveOutput - will show the Div sections below the table with more information.
     * @return string - formated table of index
     * Renders a fully functional index table.
     */
    public function RenderIndexTable($items, $contentDescriptiveOutput = true) {

        $content = null;

        ob_start();

        echo '<ol>';

        foreach ($items as $key => $value) {

            if (is_array($value)) {

                $content .= '<div class="tableHeadingSection" id="'.$key.'">' . $key . '</div>';

                $content .= $this->outputIndexItems($key, $value);
            } else {
                if (is_numeric($key))
                    echo '<li class="indexHeading"><a href="#' . $value . '">' . $value . '</a></li>';
                else {
                    echo '<li class="indexHeading"><a href="#' . $key . '">' . $key . '</a></li>';

                    $content .= '<div class="widget"><div id="' . $key . '" class="indexTableContentHeading">' . $key . '</div>
                        <p>' . $this->formatCode($value) . '</p>
                        </div>
                    ';
                }
            }
        }

        echo '</ol><hr>';

        if($contentDescriptiveOutput)
            echo $content;

        $table = ob_get_clean();

        return $table;
    }

    /**
     *
     * @param type $keys
     * @param type $items
     * @return string - creates headings within the menu for the RenderTableIndex method
     */
    private function outputIndexItems($keys, $items) {

        $content = null;

        echo '<li class="indexHeading"><a href="#' . $keys . '">' . $keys . '</a><ul>';

        foreach ($items as $key => $item) {

            if (is_array($item)) {

                $content .= '<div class="tableHeadingSection" id="'.$key.'">' . $key . '</div>';

                $this->outputIndexItems($key, $item);
            } else {
                if (is_numeric($key))
                    echo '<li><a href="#' . $item . '">' . $item . '</a></li>';
                else {
                    echo '<li><a href="#' . $key . '">' . $key . '</a></li>';

                    $content .= '<div class="widget"><div id="' . $key . '" class="indexTableContentHeading">' . $key . '</div>
                        <p>' . $this->formatCode($item) . '</p>
                        </div>
                    ';
                }
            }
        }

        echo '</ul></li>';

        return $content . '<hr>';
    }

    /**
     *
     * @param type $index
     * @return string - format code for displaying on html page.
     */
    public function formatCodeToHTML($index) {

//        $indexes = explode('<li>' , $code);
//
//        $reconstructed = null;
//
//        foreach($indexes as $index){
//
//            if(strpos($index, '<div class="code">') > 0){

                $index = str_replace('<php', '&#60;php', $index);
                $index = str_replace('<', '&#60;', $index);
                $index = str_replace('&#60;br />', '<br />', $index);
                $index = str_replace('&#60;p>', '<p>', $index);
                $index = str_replace('&#60;/p>', '</p>', $index);
                $index = str_replace('&#60;/ol>', '</ol>', $index);
                $index = str_replace('&#60;/li>', '</li>', $index);
                $index = str_replace('&#60;li', '<li', $index);
                $index = str_replace('&#60;ol', '<ol', $index);
                $index = str_replace('&#60;div', '<div', $index);
                $index = str_replace('&#60;/div', '</div', $index);

//            }
//
//            $reconstructed .= $index . '<li>';
//        }

        return $index;

    }

    /**
     *
     * @param type $code
     * @return string - format code all together, code for html and colors.
     */
    public function formatCode($code){

        $code = $this->formatCodeToHTML($code);
        $code = $this->formatCodeColors($code);

        return $code;
    }

    /**
     *
     * @param type $code
     * @return string - format html code colors.
     */
    public function formatCodeColors($code){

        $replace = array(

            '&#60;php' => '<span class="red">&#60;php</span>',
            'if(' => '<span class="blue"> if ( </span>',
            '->' => '<span class="green"> -> </span>',
            '(' => '<span class="orange"> ( </span>',
            ')' => '<span class="orange"> )</span>',
            ' new ' => '<span class="lightgreen"> new </span>',
            ';' => '<span class="yellow"> ;</span>',
            '$this' => '<span class="this lightblue"> $this</span>',
            '$' => '<span class="brown"> $</span>',
            '::' => '<span class="blue"> <b>::</b> </span>',
            'Configs/' => '<b>Configs/</b>'

        );

        $code = $this->replaceWithMultiple($code, $replace);

        return $code;
    }

    /**
     *
     * @param type $haystack
     * @param type $needlesArray
     * @return string - Run a multiple search and replace function.
     */
    public function replaceWithMultiple($haystack, $needlesArray){

        foreach($needlesArray as $key => $value){

            $haystack = str_replace($key, $value, $haystack);

        }

        return $haystack;
    }

    public function ToUpperCase($string){

        $string = strtoupper($string);

        return $string;
    }

    public function ToLowerCase($string){

        $string = strtolower($string);

        return $string;
    }

    public function FirstCharacterToUpperCase($string){

        $string = strtoupper(substr($string, 0, 1)) .  substr($string, 1);

        return $string;
    }

    public function FirstCharacterToLowerCase($string){

        $string = strtolower(substr($string, 0, 1)) .  substr($string, 1);

        return $string;
    }

    public function SpaceToBreak($string){

        $string = str_replace(' ', '<br />', $string);

        return $string;
    }

    public function RenderRows($array){

        $index = 1;

        $rows = null;

        foreach($array['tbody'] as $arr){

            if(isset($array['ignoreFields'])){

                foreach($array['ignoreFields'] as $ignore){

                    if(is_array($arr))
                           unset($arr[$ignore]);
                   else if(is_object($arr))
                           unset($arr->$ignore);

                }
            }

            if($index%2 == 0)
                $rows .= '<tr class="even">';
            else
                $rows .= '<tr class="odd">';

            if(is_array($arr) || is_object($arr)){

                foreach($arr as $item){

                    $rows .= '<td>' . $item . '</td>';
                }

            }
            else
                $rows .= '<td>' . $arr . '</td>';

            $rows .= '</tr>';

            $index++;

        }

        return $rows;

    }

    public function RenderTable($array){

        $index = 1;

        $rows = null;

        if(isset($array['title']))
            $rows = '<div class="title"><h6>'.$array['title'].'</h6></div>';

        $rows .= '
            <table class="'.@$array['class'].'" id="'.@$array['id'].'"><thead><tr>';

        if(isset($array['thead'])){

            foreach($array['thead'] as $arr){

                $rows .= '<th>'.$arr.'</th>';

            }
        }
        else{

            foreach($array['tbody'] as $arr){

                if(isset($array['ignoreFields'])){

                    foreach($array['ignoreFields'] as $ignore){

                        if(is_array($arr)){
                            unset($arr[$ignore]);
                        }
                        else if(is_object($arr)){
                            unset($arr->$ignore);
                        }

                    }
                }

                foreach($arr as $key => $val)
                    $rows .= '<th>'.str_replace('_', ' ', $this->FirstCharacterToUpperCase ($key)).'</th>';

                break;

            }
        }

        if(isset($array['actions'])){

            $rows .= '<th>Actions</th>';
        }

        $rows .= '</tr></thead><tbody>';

        foreach($array['tbody'] as $arr){

            if(isset($array['ignoreFields'])){

                foreach($array['ignoreFields'] as $ignore){

                    if(is_array($arr)){
                        unset($arr[$ignore]);
                    }
                    else if(is_object($arr)){
                        unset($arr->$ignore);
                    }

                }
            }

            if($index%2 == 0)
                $rows .= '<tr id="record_'.$index.'" class="even">';
            else
                $rows .= '<tr id="record_'.$index.'" class="odd">';

            if(is_array($arr) || is_object($arr)){

                foreach($arr as $item){

                    $rows .= '<td>' . $item . '</td>';
                }

                if(isset($array['actions'])){

                    $rows .= '<td class="actions">

                        <div class="settings"></div>

                                <div class="settingsMenu">';

                    foreach($array['actions'] as $key => $action){

                        if(isset($action['message'])){

                            $rows .= '<input type="hidden" value="'.@(isset($action['route']) ? $this->setRoute($action['route'], array($action['routeParam'] => (is_object($arr) ? $original->$action['dataparam'] : $original[$action['dataParam']] ) ) ) : $action['url'] ).'">';

                            $rows .= ' <span class="confirmAction '.@$action['class'].'" id="'.$key.'_'.$index.'">'.$key.'</span> ';

                            $rows .= '<input type="hidden" value="'.$action['message'].'">';

                        }
                        else{

                            $rows .= ' <a id="'.$key.'_'.$index.'" target="'.@$action['target'].'" class="'.@$action['class'].'" href="'.@(isset($action['route']) ? $this->setRoute($action['route'], array($action['routeParam'] => (is_object($arr) ? $original->$action['dataParam'] : $original[$action['dataParam']] ) ) ) : @$action['url'] ).'">'.$key.'</a> ';
                        }

                        $rows .= '<br />';

                    }

                    $rows .= '</div></td>';

                }

            }
            else
                $rows .= '<td>' . $arr . '</td>';

            $rows .= '</tr>';

            $index++;

        }

        $rows .= '</tbody><tfoot><tr>';

        if(isset($array['tfoot']))
            foreach($array['tfoot'] as $arr){

                $rows .= '<td>'.$arr.'</td>';

            }

        $rows .= '</tr></tfoot></table>';

        return $rows;
    }

    public function renderTableHead($array){

        if(isset($array['title']))
            $rows = '<div class="title"><h6>'.$array['title'].'</h6></div>';

        $rows .= '
            <table class="'.@$array['class'].'" id="'.@$array['id'].'"><thead><tr>';

        foreach($array['thead'] as $arr){

            $rows .= '<th>'.$arr.'</th>';

        }

        $rows .= '</tr></thead>';

        return $rows;
    }

    public function RenderSections($sectionData){

        if(is_array($sectionData)){
            $sections = '<div class="Sections">';

            $index = 1;
            foreach($sectionData as $title => $section){

                $sections .= '<div class="section '.$section['class'].'" id="section'.$index.'">';
                $sections .= '<div class="title"><h6>'.$title.'</h6></div>';

                    $sections .= '<div class="sectionHeader">';
                    $sections .= $section['header'];
                    $sections .= '</div>';

                    $sections .= '<div class="sectionBody">';
                    $sections .= $section['body'];
                    $sections .= '</div>';

                    $sections .= '<div class="sectionFooter">';
                    $sections .= $section['footer'];
                    $sections .= '</div>';

                $sections .= '</div>';

                $index += 1;

            }

            $sections .= '<div class="SectionsFooter"><div class="SectionStats">Page: <span id="Section">1</span></div><div class="SectionsButtons"><input type="button" value="Previous" class="prev"><input type="button" value="Next" class="next"></div></div>';

            $sections .= '</div>';

            return $sections;

        }
        else
            echo 'INVALID DATA FOR RENDERING SECTIONS';
    }

    /**
     * You can use the output method to filter your output character cases.<br /><br />
     * Four filters can be applied:<br />
     * upper<br />
     * lower<br />
     * firstUpper or charUpper<br />
     * firstLower or charLower<br />
     *
     */
    public function Output($string, $filter = null){

        $filter = str_replace(' ', '', $filter);

        if(!empty($filter))
            switch($filter){

                case 'upper':
                    $string = $this->ToUpperCase($string);
                    break;
                case 'lower':
                    $string = $this->ToLowerCase($string);
                    break;
                case 'firstupper':
                    $string = $this->FirstCharacterToUpperCase($string);
                    break;
                case 'firstlower':
                    $string = $this->FirstCharacterToLowerCase($string);
                    break;
                case 'spacetobreak':
                    $string = $this->SpaceToBreak($string);
                    break;
                case 'list':
                case 'orderedlist':
                    $string = '<ol><li>'.str_replace(' ', '</li><li>', $string) . '</li></ol>';
                    break;
                case 'unorderedlist':
                    $string = '<ul><li>'.str_replace(' ', '</li><li>', $string) . '</li></ul>';
                    break;
                case 'tablebody':
                case 'tablerows':
                    $string = $this->renderRows($string);
                    break;
                case 'table':
                    $string = $this->renderTable($string);
                    break;
                case 'tablehead':
                    $string = $this->renderTableHead($string);
                    break;
                case 'form':
                    $html = new HTMLGenerator();
                    $string = $html->generateForm($string);
                    break;
                case 'indextable':
                    $string = $this->RenderIndexTable($string);
                    break;
                case 'sections':
                case 'Sections':
                    $string = $this->RenderSections($string);
                    break;
                default:
                    echo 'invalid filter';
                    break;
            }

         return $string;
    }

}