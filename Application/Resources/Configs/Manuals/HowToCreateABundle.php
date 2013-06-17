<?php

Set::Config('Manuals', array(
    'HowToCreateABundle' => '
<i><h3> === SESSION ENABLED BUT CLASS :: '.\Get::Config('Auth.Login.EntityRepository').'() ENTITY COULD NOT BE FOUND === </h3></i>
                    <p>You have enabled sessions for this application in <b>application/configs/Application.php</b>, but the <b>AUTH_USER_ENTITY</b> defined as <b>\''.\Get::Config('Auth.Login.EntityRepository').'\'</b> could not be found. Either disable sessions by setting the SESSION_ENABLED config to false or create a bundle with an Entity using the following methods:
                        <ol>
                            <li>You can create it running the command line script from <b>Application/Console/index.php</b></p></li>
                            <li>You can create an entity manually following the instructions below.</li>
                        </ol>
                    <p style="color: red">
                        <br />
                        <i>Application/Configs/Auth.php</i><br /><br />
                            define (\'AUTH_USER_ENTITY\', \''.\Get::Config('Auth.Login.EntityRepository').'\') ;
                    </p>
                    <p>
                        Please take the time to setup Application/Configs/Auth.php and create bundles for the whole mechanism accordingly. i.e, a user entity bundle and a login bundle.
                    </p>
                    <p>
                    To create an entity manually, you need to create a folder inside the Bundles folder, this should be your bundle name, inside of it create a file named as Entity.php and provide with atleast
                    <ol>
                        <li>A method for getting the users data which you will be using throughout your application. Set that function name in the Auth.php config file as:</li>
                        <li>define(\'AUTH_USER_POPULATE_METHOD\', \'  YOUR METHOD GOES HERE \') ;
                    </ol>
                    </p>
                    <b>Include the bundle in the kernel.php\'s fetchAllBundles() method to include it in the application</b>
                    <p>Once you have done this, you can add more files and start building your session enabled application, you can add exception routes to Application/Routes/Auth_Bypass.php which you dont want to have session security applied to. i.e login page.
'));