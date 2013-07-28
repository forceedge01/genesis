<?php

namespace Application\Components;



class Mailer{

    private
            $phpmailer,
            $error_msg,
            $options;

    public function __construct() {

        $this->phpmailer_init();
    }

    /**
     *
     * @return boolean|\Mail - true on success, false on failure<br />
     * <br />initialize mailer class options, configurable in mail config.
     */
    private function phpmailer_init() {

        $this->phpmailer = new Mailer\PHPMailer(true);

        $options = $this->options;
        // Don't configure for SMTP if no host is provided.

        if(\Get::Config('Mailer.SMTP_HOST') == '')
            return false;

        $this->phpmailer->IsSMTP();

        $this->phpmailer->Host = \Get::Config('Mailer.SMTP_HOST');

        $this->phpmailer->Port = ((\Get::Config('Mailer.SMTP_PORT')) ? \Get::Config('Mailer.SMTP_PORT') : 25);

        $this->phpmailer->SMTPAuth = \Get::Config('Mailer.SMTP_AUTH') ? \Get::Config('Mailer.SMTP_AUTH') : false;

        if ( $this->phpmailer->SMTPAuth ) {

            $this->phpmailer->Username = \Get::Config('Mailer.SMTP_USERNAME');

            $this->phpmailer->Password = \Get::Config('Mailer.SMTP_PASSWORD');
        }

        $this->phpmailer->SMTPSecure = $options['smtp_secure'];

        $this->phpmailer->WordWrap = $options['wordwrap'];

        $this->phpmailer->SMTPDebug = \Get::Config('Mailer.SMTP_DEBUG');

        return $this;
    }

    /**
     *
     * @param array $params - $params for sending email<br />
     * Sends an email to the address specified in params
     * $param = array(
     *  'to' => '',
     *  'subject' => '',
     *  'from' => '',
     *  'from_name' => '', default APPLICATION_NAME
     *  'message' => '',
     *  'html' => boolean, default false
     * );
     */
    public function send($to, $subject, $body, array $params = array()){

        try{

            $this->phpmailer->AddAddress($to);

            $this->phpmailer->Subject = $subject;

            $this->phpmailer->From = $params['from'];

            $this->phpmailer->FromName = (!isset($params['from_name']) ? $params['html'] : \Get::Config('Application.Name') );

            $this->phpmailer->MsgHTML($body);

            $this->phpmailer->IsHTML((!@isset($params['html']) ? $params['html'] : false ));

            $this->phpmailer->Send();

            if ( $this->phpmailer->ErrorInfo != "" ) {
                    $this->error_msg  = '<div class="error"><p>' . __( 'An error was encountered while trying to send the test e-mail.', $this->textdomain ) . '</p>';
                    $this->error_msg .= '<blockquote style="font-weight:bold;">';
                    $this->error_msg .= '<p>' . $this->phpmailer->ErrorInfo . '</p>';
                    $this->error_msg .= '</p></blockquote>';
                    $this->error_msg .= '</div>';

                    trigger_error($this->error_msg);
            }

        }
        catch(Exception $e){

            echo 'Cannot send mail.'. $e->getMessage();
        }
    }

}