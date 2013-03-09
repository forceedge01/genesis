
<div class="wrapper">

    <fieldset>

        <legend>

            <h1>Welcome to <?=APPLICATION_NAME?></h1>

        </legend>

        <div class="widget index">

<!--            <table>

                <thead>

                    <tr>

                        <th>Name</th>
                        <th>Address</th>
                        <th>Post Code</th>

                    </tr>

                </thead>

                <tbody>-->

            <?=$this->Output($params['table'],'table')?>

            <br />

            <?=$this->Output($params['form'],'form')?>

<!--                </tbody>

            </table>-->

            <p>This page will tell you how to use The Genesis project to build your own web application.</p>

            <h3>Index</h3>

            <?=$this->renderIndexTable(array(

            'Introduction' => 'Project Genesis is a very novice but powerful framework built by A. Wahhab Qureshi. You can use this framework to build your own web application with dramatic speed increase.',

            'Application Methods And Classes' => array(

                'class :: Application' => '
                    <p>Use the application class for its following methods</p>

                    <ol>
                        <li>Application::hashPassword ( void $variable )</li>
                        <li>Application::generateRandomString ( string $length = 10 )</li>
                    </ol>

                    <p>Other than these methods the Application class inherits other important classes and provides the ability to use session in your application.</p>',
                'class :: Auth'=> '

                <p>The Auth class provides methods dealing with authentication, you can build a very simple yet powerful login system with its authenticate method in a few minutes. The authenticate method returns a bool if the user exists else it returns false.</p>

                    <ol>
                        <li>Auth::authenticate ( void )</li>
                    </ol>


                <div class="code"><php

                <br /><br />

                $user = new User();<br /><br />

                if($this->authenticate())<br />
                    $this->forwardTo("Home");<br /><br />

                </div>

                <p>The authenticate method will use the forms post elements provided in the Configs/Auth.php</p>

                <p>This is all you need to authenticate a user, no parameters passing needed, just add them in the config.</p>

                ',
                'class :: Cloner' => '

                <p>The cloner class provides simple methods for cloning zip files and extract them to a sub-folder which is provided in the Configs/Cloner.php</p>


                <ol>
                    <li>Cloner::CloneSite() will clone a zip specified and unzip into a folder provided as a domain.</li>
                    <li>Cloner::removeClone($id) will remove a clone if the record exists in the database.</li>
                </ol>


                ',
                'class :: Dir' => '

                <p>The Dir() class provides simple methods for directory management</p>


                <ol>
                    <li>Dir::removeDirectory ( string $directoryPath )</li>
                    <li>Dir::cleanDirectory ( string $directoryPath )</li>
                    <li>Dir::createDirectory ( string $directoryPath )</li>
                    <li>Dir::createFile ( string $filePath )</li>
                    <li>Dir::clearContentsOfFile ( string $filePath )</li>
                    <li>Dir::deleteFile ( string $filePath )</li>
                    <li>Dir::readFile ( string $filePath )</li>
                </ol>


                ',
                'Class :: Database' => '
                    <p>The database class provides with comprehensive methods to do very safe but efficient query executions. You can use safe methods that will use parameters to construct a query.</p>

                    <ol>
                        <li>Database::Query ( string $sqlQuery )</li>
                        <li>Database::multiQuery ( void )</li>
                        <li>Database::DropDatabase ( string $databaseName )</li>
                        <li>Database::importSQLFromFile ( string $filePath )</li>
                        <li>Database::RollBack ( void )</li>
                        <li>Database::Commit ( void )</li>
                        <li>Database::BeginTransaction ( void )</li>
                        <li>Database::CloseConnection ( void )</li>
                        <li>Database::Update ( string $table, array $params )</li>
                        <li>Database::Insert ( string $table, array $params )</li>
                        <li>Database::Delete ( string $table, array $params )</li>
                    </ol>

                    ',
                'Class :: Debugger' => '
                    <p>The debugger class gives you two simple but very helpful methods to debug your application errors at any point, other than that it also gives you the ability to backtrace your erros.</p>

                    <ol>
                        <li>Debugger::pre ( void $variable )</li>
                        <li>Debugger::debug ( void )</li>
                    </ol>

                 ',
                'Class :: Mailer' => '
                    <p>The Mailer() class provides methods for SMTP emailing using the PHP-mailer class, you can configure your own SMTP details in Application/Configs/Mail.php</p>

                    <ol>
                        <li>Mailer::send ( array $params )</li>
                    </ol>',

                'Class :: Router' => '
                    <p>The Router() class provides methods for routing and forwarding to different routes.</p>

                    <ol>
                        <li>Router::getPattern ( void )</li>
                        <li>Router::GetParams ( void )</li>
                        <li>Router::forwardRequest ( void )</li>
                        <li>Router::setRoute ( string $routeName, void $variable to pass on )</li>
                        <li>Router::getRoute ( string $routeName )</li>
                        <li>Router::getRawRoute ( string $routeName )</li>
                        <li>Router::getController ( string $routeName )</li>
                        <li>Router::forwardTo ( string $routeName, array $urlQueryString )</li>
                        <li>Router::forwardToController ( string $routeName, string $variableName )</li>
                        <li>Router::extractVariable ( string $routeName )</li>
                        <li>Router::reconstructPattern ( string $routeName )</li>
                        <li>Router::extractAndReplaceVariable ( void )</li>
                        <li>Router::checkExceptionRoutes ( void )</li>
                    </ol>
                 ',
                'Class :: Template' => '
                    <p>The Template() class provides methods for easier templating, checks and rendering certain elements.</p>

                    <ol>
                        <li>Template::Render ( string $template, array $params )</li>
                        <li>Template::RenderTemplate ( string $template, array $params )</li>
                        <li>Template::IncludeCSS ( string $asset, string params )</li>
                        <li>Template::IncludeJS ( string $asset, string params )</li>
                        <li>Template::IncludeImage ( string $asset, string params )</li>
                        <li>Template::Asset ( string $asset )</li>
                        <li>Template::setAsset ( string $asset, string params )</li>
                        <li>Template::setFlash ( array $Message )</li>
                        <li>Template::setError ( array $Error )</li>
                        <li>Template::IfExistsElse ( void $if, void $else, expression $operator :: optional )</li>
                        <li>Template::Errors ( void )</li>
                        <li>Template::FlashAll ( void )</li>
                        <li>Template::RenderIndexTable ( array $tableIndex )</li>
                        <li>Template::formatCodeToHTML ( string $html )</li>
                    </ol>

                    ',
                'Class :: Zip' => '
                    <p>The zip class makes it easier to create zip files out of files and directories.</p>

                    <ol>
                        <li>Zip::addDirectory ( void $variable )</li>
                        <li>Zip::ZipFromFile ( void )</li>
                        <li>Zip::ZipFromDirectory ( string $length = 10 )</li>
                        <li>Zip::unzip ( string $pathToZip )</li>
                        <li>Zip::Close ( void )</li>
                    </ol>

                    '
            ),

            'Application Configs' => array(

                'Pre-defined Configs' => array(

                    'Application',
                    'Auth',
                    'Cloner',
                    'Database',
                    'Mailer'

                ),

                'Creating a Config File',

                'Using a config file'

            ),

            'Routing' => array(

                'Creating a Routes File',
                'Creating Routes',
                'Mapping Routes to Controller Actions'

            ),

            'Controllers' => array(

                'Creating Controllers',
                'Creating Routes for Controllers',
                'Coupling Controllers to Templates',
                'Coupling Controllers to Entities'

            ),

            'Views' => array(

                'Creating Views',
                'Views, Routes, Controllers'

            ),

            'Entites/Models' => array(

                'Creating a New Entity',
                'Using Entity in Controllers'

            ),

            'Creating Bundles' => array(

                'What is a bundle',
                'Creating a new bundle',
                'Structure of a bundle',
                'Customizing a bundle'

            ),

            'Templating with Genesis' => array(

                'RenderTemplate',
                'Render',
                'RenderIndexTable',
                'setRoute',
                'setAsset',
                'FlashAll',
                'includeCSS',
                'includeJS',
                'includeImage',
                'setFlash',
                'setError',
                'ifExistsElse'

            ),

            'Creating a login system with the Auth component'

        ), true)?>

        </div>

    </fieldset>

</div>