<?php

namespace Bundles\Welcome\Controllers;



use Application\Core\Controllers\ApplicationController;

use Bundles\Welcome\Interfaces\WelcomeControllerInterface;


final class WelcomeController extends ApplicationController implements WelcomeControllerInterface {

    public
            $htmlgen;

    public function indexAction() {

        $params['PageTitle'] = 'Welcome to ' . APPLICATION_NAME;

        $params['table'] = array(

            'class' => 'dTable paginate',
            'id' => 'AwesomeTable',

            'thead' => array(

                'Name',
                'Address',
            ),

            'tbody' => array(

                '0' => array(

                    'ID' => '1',
                    'Name' => 'A. Wahhab',
                    'Address' => '182 Burlington Road',
                ),

                '1' => array(

                    'ID' => '2',
                    'Name' => 'Qureshi',
                    'Address' => 'dafdasdf Burlington Road',
                ),

                '3' => array(

                    'ID' => '3',
                    'Name' => 'A. Wahhab',
                    'Address' => '182 Burlington Road',
                ),
            ),

            'actions' => array(

                'view' => array(

                    'route' => 'Welcome_View',
                    'routeParam' => 'id',
                    'dataParam' => 'ID',
                    'message' => 'Are you sure you want to do this?',
                ),
                'list' => array(

                    'route' => 'Welcome_List'
                ),
            ),

            'ignoreFields' => array(

                'ID'
            )

        );

        $params['form'] = array(

            'action' => $this->setRoute('Welcome_View'),
            'class ' => 'confirm',

            'inputs' => array(

                'text' => array(

                    'label' => 'Email',
                    'value' => 'some value',
                    'name' => 'email',
                ),

                'textarea' => array(

                    'label' => 'Textarea',
                    'value' => 'bla',
                    'name' => 'blabla',
                ),
            ),

            'submission' => array(

                'submit' => array(
                    'value' => 'Submit this form',
                 ),

                'reset' => array(
                    'value' => 'reset this form'
                ),

            )

        );

        $params['sections'] = array(

            'type' => 'accordian',
            'title' => 'This is a title',

            'sections' => array(

                'Normal Section' => array(

                    'header' => 'this is one header',
                    'body' => 'abc',
                    'footer' => 'and a footer',

                ),

                '2nd Normal Section' => array(

                    'header' => '2nd header',
                    'body' => '2nd body',
                    'footer' => '2nd footer',

                ),

                '3nd Normal Section' => array(

                    'header' => '2nd header',
                    'body' => '2nd body',
                    'footer' => '2nd footer',

                ),

             )

        );

        $this->htmlgen = $this->GetComponent('HTMLGenerator');

        $this->Render('Welcome:index.html.php', $params);
    }

}
