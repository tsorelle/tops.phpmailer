<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/1/2017
 * Time: 3:56 PM
 */

use PHPMailer\PHPMailer\PHPMailer;
use Tops\mail\TPhpMailer;
use PHPUnit\Framework\TestCase;

/**
 * Class TPhpMailerTest
 *
 * It is recommended to use an smtp dummy app such as
 * smtpdummy http://cylog.org/tools/smtpdummy.jsp
 *
 */
class TPhpMailerTest extends TestCase
{
    private $enabled;

    private function checkTestsDisabled() {
        if (!isset($this->enabled)) {
            $this->enabled = @mail('test@foo.com', 'Testing server availability', 'Ok');
            if (!$this->enabled) {
                print 'Warning: Test mail server is not running. Tests disabled. Need to run a dummy smtp server (such as smtp4dev) for these tests. ' . "\n";
                $this->assertTrue(true);
            }
        }
        return (!$this->enabled);
    }


    public function testPhpMailerIsMail() {
        if ($this->checkTestsDisabled()) {
            return;
        }
        $mail = new PHPMailer;
        $mail->setFrom('from@example.com', 'First Last');
        $mail->addReplyTo('replyto@example.com', 'First Last');
        $mail->addAddress('whoto@example.com', 'John Doe');
        $mail->Subject = 'PHPMailer mail() test';
        $mail->msgHTML("<h2>Hello world</h2>");
//Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
//Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        $actual = $mail->send();
        $this->assertTrue($actual,$mail->ErrorInfo);
    }
    public function testPhpMailer() {
        if ($this->checkTestsDisabled()) {
            return;
        }

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        // $mail->isSMTP();
        $mail->isMail();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
        // $mail->SMTPDebug = 2;
//Set the hostname of the mail server
        // $mail->Host = 'localhost';
//Set the SMTP port number - likely to be 25, 465 or 587
        // $mail->Port = 25;
//Whether to use SMTP authentication
//        $mail->SMTPAuth = true;
//Username to use for SMTP authentication
//        $mail->Username = 'yourname@example.com';
//Password to use for SMTP authentication
//        $mail->Password = 'yourpassword';
//Set who the message is to be sent from
        $mail->setFrom('tls@2quakers.net', 'Terry SoRelle');
//Set an alternative reply-to address
        // $mail->addReplyTo('replyto@example.com', 'First Last');
//Set who the message is to be sent to
        $mail->addAddress('terry.sorelle@outlook.com', 'The TEster');
//Set the subject line
        $mail->Subject = 'PHPMailer SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
        // $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));

        $mail->isHTML(true);
        $mail->msgHTML('<strong>This</strong> <em>is a test</em>');
        // $mail->Body = '<strong>This</strong> <em>is a test</em>';
        // $mail->AltBody = 'Testing me';
        // $mail->Body = 'Text version';
//Replace the plain text body with one created manually
        // $mail->AltBody = 'This is a plain-text message body';
//Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
        $actual = $mail->send();
        $this->assertTrue($actual,$mail->ErrorInfo);
    }
    public function testTPhpMailer() {
        if ($this->checkTestsDisabled()) {
            return;
        }

        $msg = new \Tops\mail\TEMailMessage();
        $msg->addRecipient('tls@2quakers.net','Terry SoRelle');
        $msg->setSubject('Test message');
        $msg->setMessageBody('Hello world');
        $msg->setFromAddress('admin@foo.com','Administrator');

        $mailer = new TPhpMailer();
        $result = $mailer->send($msg);
        $this->assertTrue($result);

    }

    /*
    public function testTextMail() {
        $actual = class_exists('Tops\\mail\\TPhpMailer');
        $this->assertTrue($actual);
        $msg = new \Tops\mail\TEMailMessage();
        $msg->addRecipient('tester@testing.com','Tom Tester');
        $msg->setFromAddress('mrpeanut@peanut.com','Mister Peanut');
        $msg->setSubject('A simple test');
        $msg->setMessageBody("This is a unit test calling. \nHello world.");
        $mailer = new TPhpMailer();
        $actual = $mailer->send($msg);
        $expected = true;
        $this->assertEquals($expected,$actual);
    }
*/
}
