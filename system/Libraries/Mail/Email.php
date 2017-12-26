<?php namespace System\Libraries\Mail;

/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5
 * @package PHPMailer
 * @link https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 - 2014 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * PHPMailer - PHP email creation and transport class.
 * @package PHPMailer
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 */

use PHPMailer\PHPMailer\PHPMailer;

class Email extends PHPMailer
{


    private $config;


    /**
     * Email constructor.
     */
    function __construct ()
    {

        parent::__construct ();


        $this->config = config ('mail');

        $this->isSMTP ();

        $this->SMTPAuth = true;

        $this->SMTPSecure = $this->config[ 'smtp_secure' ];

        $this->isHTML ( $this->config[ 'html' ] );

        $this->Host = $this->config[ 'host' ];

        $this->Username = $this->config[ 'username' ];

        $this->Password = $this->config[ 'password' ];

        $this->Port = $this->config[ 'port' ];

        $this->CharSet = $this->config[ 'charset' ];

        $this->SMTPDebug = $this->config[ 'show_error' ];
    }


    /**
     * @param    string $email
     * @param    string $name
     * @return Email
     */
    public function from ( $email , $name = null )
    {
        $this->From = $email;
        $this->AddReplyTo ( $email , $name );

        if (!is_null($name))
        {
          $this->FromName = $name;
        }


        return $this;
    }

    /**
     * @param    string $email
     * @param    string $name
     * @return    Email
     */
    public function to ( $email , $name = null )
    {
        $this->AddAddress ( $email , $name );
        return $this;
    }

    /**
     * @param    string $subject
     * @return    Email
     */
    public function subject ( $subject )
    {
        $this->Subject = $subject;
        return $this;
    }

    /**
     * @param    string $message
     * @return    Email
     */
    public function message ( $message )
    {
        $this->Body = $message;
        return $this;
    }


    /**
     * PhpMailer Destruct
     */
    function __destruct ()
    {
        parent::__destruct ();
    }

}
