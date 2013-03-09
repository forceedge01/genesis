<?php

class Mail{

    private
            $phpmailer,
            $error_msg;

    public function __construct() {

        $this->phpmailer_init();
    }

    /**
     *
     * @return boolean|\Mail - true on success, false on failure<br />
     * <br />initialize mailer class options, configurable in mail config.
     */
    private function phpmailer_init() {

        $this->phpmailer = new PHPMailer(true);

        $options = $this->options;
        // Don't configure for SMTP if no host is provided.

        if(MAIL_SMTP_HOST == '')
            return false;

        $this->phpmailer->IsSMTP();

        $this->phpmailer->Host = MAIL_SMTP_HOST;

        $this->phpmailer->Port = ((MAIL_SMTP_PORT) ? MAIL_SMTP_PORT : 25);

        $this->phpmailer->SMTPAuth = MAIL_SMTP_AUTH ? MAIL_SMTP_AUTH : false;

        if ( $this->phpmailer->SMTPAuth ) {

            $this->phpmailer->Username = MAIL_SMTP_USERNAME;

            $this->phpmailer->Password = MAIL_SMTP_PASSWORD;
        }

        $this->phpmailer->SMTPSecure = $options['smtp_secure'];

        $this->phpmailer->WordWrap = $options['wordwrap'];

        $this->phpmailer->SMTPDebug = MAIL_SMTP_DEBUG;

        return $this;
    }

    /**
     *
     * @param array $params - $params for sending email<br />
     * Sends an email to the address specified in params
     */
    public function send($params){

        try{

            $this->phpmailer->AddAddress($params['to']);

            $this->phpmailer->Subject = $params['subject'];

            $this->phpmailer->From = $params['from'];

            $this->phpmailer->FromName = (!isset($params['from_name']) ? $params['html'] : APPLICATION_NAME );

            $this->phpmailer->MsgHTML($params['message']);

            $this->phpmailer->IsHTML((!@isset($params['html']) ? $params['html'] : false ));

            $this->phpmailer->Send();

            if ( $this->phpmailer->ErrorInfo != "" ) {
                    $this->error_msg  = '<div class="error"><p>' . __( 'An error was encountered while trying to send the test e-mail.', $this->textdomain ) . '</p>';
                    $this->error_msg .= '<blockquote style="font-weight:bold;">';
                    $this->error_msg .= '<p>' . $this->phpmailer->ErrorInfo . '</p>';
                    $this->error_msg .= '</p></blockquote>';
                    $this->error_msg .= '</div>';

                    echo $this->error_msg;
            }

        }
        catch(Exception $e){

            echo 'Cannot send mail.'. $e->getMessage();
        }
    }

}