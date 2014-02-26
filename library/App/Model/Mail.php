<?php

class App_Model_Mail extends Zend_Mail {

    protected $_mail = null;
    
    public function __construct( $encoding='UTF-8' ) {
        $this->_mail = new Zend_Mail($encoding);
    }
    
    public function send($transport=null) {
        $this->_mail->send($transport);
    }
    
    public function prepare(array $config) {
        
        if ( is_array($config['header']) ) {
            foreach($config['header'] as $hk=>$hv) {
                if ( is_array($hv) ) {
                    $this->_mail->addHeader($hk, $hv[0], $hv[1]);
                } else {
                    $this->_mail->addHeader($hk, $hv);
                }
            }
        }
        
        if ( is_array($config['from']) ) {
            list($name, $mail) = each($config['from']); 
            $this->_mail->setFrom($mail, $name);
        }
        
        if ( is_array($config['to']) ) {
            $this->_mail->addTo($config['to']);
        }
        
        if ( is_array($config['cc']) ) {
            $this->_mail->addCc($config['cc']);
        }

        if ( is_array($config['bcc']) ) {
            $this->_mail->addBcc($config['bcc']);
        }

        if ( isset($config['subject']) ) {
            $this->_mail->setSubject($config['subject']);
        }

        if ( isset($config['body']) ) {
            $this->_mail->setBodyHtml($config['body']);
        }

        if ( is_array($config['bodyTemplate']) ) {
            $template = new App_Model_Template(); 
            $template->setSuffixTemplatePath('mail'); // Append this to [DEFAULT PATH]/
            if ( is_array($config['bodyTemplate']['vars']) ) {
                $content = $template->renderTemplate($config['bodyTemplate']['templateName'], $config['bodyTemplate']['vars']);
            } else {
                $content = $template->renderTemplate($config['bodyTemplate']['templateName']);
            }
            $this->_mail->setBodyHtml($content);
        }

        if ( is_array($config['attachment']) ) {
            foreach($config['attachment'] as $attk=>$attv) {
                if ( is_array($attv) ) {
                    $this->addAttachmentAuto($attv[0], $attv[1]);
                } else {
                    $this->addAttachmentAuto($attv);
                }
            }
        }
        
    }

    
    public function addAttachmentAuto($fileName, $path='') {
        $at = new Zend_Mime_Part($path.$fileName);
        $at->type        = $this->getMIMEType($fileName);
        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding    = Zend_Mime::ENCODING_BASE64;
        $at->filename    = $fileName;
        $this->_mail->addAttachment($at);    
    }
    
    protected function getMIMEType($filename) {
        preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);
        switch(strtolower($fileSuffix[1])) {
            case "js" :
                return "application/x-javascript";

            case "json" :
                return "application/json";

            case "jpg" :
            case "jpeg" :
            case "jpe" :
                return "image/jpg";

            case "png" :
            case "gif" :
            case "bmp" :
            case "tiff" :
                return "image/".strtolower($fileSuffix[1]);

            case "css" :
                return "text/css";

            case "xml" :
                return "application/xml";

            case "doc" :
            case "docx" :
                return "application/msword";

            case "xls" :
            case "xlt" :
            case "xlm" :
            case "xld" :
            case "xla" :
            case "xlc" :
            case "xlw" :
            case "xll" :
                return "application/vnd.ms-excel";

            case "ppt" :
            case "pps" :
                return "application/vnd.ms-powerpoint";

            case "rtf" :
                return "application/rtf";

            case "pdf" :
                return "application/pdf";

            case "html" :
            case "htm" :
            case "php" :
                return "text/html";

            case "txt" :
                return "text/plain";

            case "mpeg" :
            case "mpg" :
            case "mpe" :
                return "video/mpeg";

            case "mp3" :
                return "audio/mpeg3";

            case "wav" :
                return "audio/wav";

            case "aiff" :
            case "aif" :
                return "audio/aiff";

            case "avi" :
                return "video/msvideo";

            case "wmv" :
                return "video/x-ms-wmv";

            case "mov" :
                return "video/quicktime";

            case "zip" :
                return "application/zip";

            case "tar" :
                return "application/x-tar";

            case "swf" :
                return "application/x-shockwave-flash";

            default :
                if( function_exists("mime_content_type") ) {
                    return mime_content_type($filename);
                }
        }
        return "unknown/" . trim($fileSuffix[0], ".");
    }
  
    
    
}

?>