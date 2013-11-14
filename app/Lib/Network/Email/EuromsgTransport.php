<?php

 /**
        * CakePHP EuromsgTransport for sending mails over euromsg webservices. 
        * @version 0.1
        * @author gcg
        * @license       http://www.opensource.org/licenses/mit-license.php MIT License
        * 
        *
        * Example mail config array in app/Config/email.php 
        * public $euromsg = array(
        *       'from' => array('example@example.com' => 'Example Site'),
        *       'FromName' => 'Example Site', 
        *       'FromAddress' => 'example@example.com',
        *       'ReplyAddress' => 'reply@fitandcolor.com',
        *       'url' => 'http://ws.euromsg.com/ecomm/',
        *       'username' => 'euromsg username',
        *       'password' => 'euromsg password',
        *       'transport' => 'Euromsg'
        *   );
        *
        *
        *    FromName and FromAddress variables must be the same as you enter in your configuration settings at euromsg
        *
        *
    **/

App::uses('AbstractTransport', 'Network/Email');

class EuromsgTransport extends AbstractTransport {

	public $url; 
    public $username;
    public $password;

    public $ticket = null;

    public function send(CakeEmail $email) {

        $this->url = $this->_config['url'];
        $this->username = $this->_config['username'];
        $this->password = $this->_config['password'];


        $this->login();

        $wsPost = new SoapClient($this->url . "post.asmx?wsdl");

        $eol = PHP_EOL;
        if (isset($this->_config['eol'])) {
            $eol = $this->_config['eol'];
        }

        $eol = PHP_EOL;
        if (isset($this->_config['eol'])) {
            $eol = $this->_config['eol'];
        }
        $headers = $email->getHeaders(array('from', 'sender', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'bcc'));
        $to = $headers['To'];
        
        unset($headers['To']);
        $headers = $this->_headersToString($headers, $eol);
        $message = implode($eol, $email->message());

        $emailRequest = new EMPostRequest();
        $emailRequest->ServiceTicket = $this->ticket;

        $emailRequest->FromName = $this->_config['FromName'];
        $emailRequest->FromAddress = $this->_config['FromAddress'];
        $emailRequest->ReplyAddress = $this->_config['ReplyAddress'];

        $emailRequest->ToEmailAddress = $to;
        $emailRequest->Subject = mb_decode_mimeheader($email->subject());
        $emailRequest->HtmlBody = $message;


        $rPost = $wsPost->PostHtml($emailRequest)->PostHtmlResult;
        
        $PostID = -1;

        if (intval($rPost->Code) != 0){
           
            $this->login();
            $emailRequest->ServiceTicket = $this->ticket;
            $rPost = $wsPost->PostHtml($emailRequest)->PostHtmlResult;
            
        }

        unset($wsPost,$emailRequest,$rPost);
        
        return true;
    }

    /**
        * login to the euromsg webservices
        *
        * @author gcg
    **/
    public function login(){
        $wsLogin = new SoapClient($this->url . "auth.asmx?wsdl");

        try {
            $this->ticket = $wsLogin->Login(
                (object) array(
                    "Username" => $this->username,
                    "Password" => $this->password
                ))->LoginResult->ServiceTicket;
        } catch (Exception $e) {
            
        }

    }

}


 /**
        * euromsg send object class, doesnt do shit actually just a variable holder. 
        *
        * @author euromsg
    **/
class EMPostRequest{
    /**
    * ServiceTicket
    *
    * @var string
    **/
    public $ServiceTicket;
    /**
    * FromName
    *
    * @var string
    **/
    public $FromName = "";
    /**
    * FromAddress
    *
    * @var string
    **/
    public $FromAddress = "";
    /**
    * ReplyAddress
    *
    * @var string
    **/
    public $ReplyAddress = "";
    /**
    * Subject
    *
    * @var string
    **/
    public $Subject;
    /**
    * HtmlBody
    *
    * @var string
    **/
    public $HtmlBody;
    /**
    * Charset
    *
    * @var string
    **/
    public $Charset = "UTF-8";
    /**
    * ToName
    *
    * @var string
    **/
    public $ToName = "";
    /**
    * ToEmailAddress
    *
    * @var string
    **/
    public $ToEmailAddress;
    /**
    * Attachments
    *
    * @var binary
    **/
    public $Attachments;
}