<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/11/2 22:45:30
 */

namespace Weline\Smtp\Helper;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Weline\Framework\App\Exception;

class SmtpSender extends \Weline\Framework\App\Helper
{
    /**
     * @var \Weline\Smtp\Helper\Data
     */
    private Data $data;

    function __construct(
        Data $data
    )
    {
        $this->data = $data;
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/11/2 22:50
     * 参数区：
     *
     * @param string|array $from
     * @param string|array $to
     * @param string       $subject
     * @param string       $content
     * @param string       $alt
     * @param string|array $attachment
     * @param string|array $reply_to
     * @param string|array $cc
     * @param string|array $bcc
     * @param string       $module
     *
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Weline\Framework\App\Exception
     */
    function sender(
        string|array $from,
        string|array $to,
        string       $subject,
        string       $content,
        string       $alt = '',
        string|array $attachment = '',
        string|array $reply_to = '',
        string|array $cc = '',
        string|array $bcc = '',
        string       $module = 'Weline_Smtp'
    )
    {
        // TODO 解决邮件发送乱码问题
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $mail->addCustomHeader('charset','UTF-8');
        $mail->addCustomHeader('Content-Transfer-Encoding','8Bit');

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                                                                                                                                                                                                                                                                                                                                                      //Enable verbose debug output
        $mail->isSMTP();                                                                                                                                                                                                                                                                                                                                                                            //Send using SMTP
        $mail->Host = $this->data->get($this->data::smtp_host, $module);


        //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                                                                                                                                                                                                                                                                                                                                                     //Enable SMTP authentication
        $mail->Username = $this->data->get($this->data::smtp_username, $module);
        //SMTP username
        $mail->Password = $this->data->get($this->data::smtp_password, $module);
        //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                                                                                                                                                                                                                                                                                                                                            //Enable implicit TLS encryption
        $mail->Port       = $this->data->get($this->data::smtp_port, $module);
        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        // 发送者
        if (is_string($from)) {
            $mail->setFrom($from);
        } else {
            $mail->setFrom($from['email'], $from['name'] ?? '');
        }
        // 接受者
        if (is_string($to)) {
            $mail->addAddress($to);               //Name is optional
        } else {
            if (isset($to['email'])) {
                $mail->addAddress($to['email'], $to['name'] ?? '');               //Name is optional
            } else {
                foreach ($to as $to_email) {
                    if (isset($to_email['email'])) {
                        $mail->addAddress($to_email['email'], $to_email['name'] ?? '');               //Name is optional
                    }
                }
            }
        }
        if (!$mail->getToAddresses()) {
            throw new Exception(__('接受者邮箱为空：请正确设置接收邮箱！'));
        }

        // 回复地址
        if ($reply_to) {
            if (is_string($reply_to)) {
                $mail->addReplyTo($reply_to);               //Name is optional
            } else {
                if (isset($reply_to['email'])) {
                    $mail->addReplyTo($reply_to['email'], $reply_to['name'] ?? '');               //Name is optional
                } else {
                    foreach ($reply_to as $reply_to_email) {
                        if (isset($reply_to_email['email'])) {
                            $mail->addReplyTo($reply_to_email['email'], $reply_to_email['name'] ?? '');               //Name is optional
                        }
                    }
                }
            }
        }
        // 抄送
        if ($cc) {
            if (is_string($cc)) {
                $mail->addCC($cc);               //Name is optional
            } else {
                if (isset($cc['email'])) {
                    $mail->addCC($cc['email'], $cc['name'] ?? '');               //Name is optional
                } else {
                    foreach ($cc as $cc_email) {
                        if (isset($cc_email['email'])) {
                            $mail->addCC($cc_email['email'], $cc_email['name'] ?? '');               //Name is optional
                        }
                    }
                }
            }
        }
        // 密送
        if ($bcc) {
            if (is_string($bcc)) {
                $mail->addBCC($bcc);               //Name is optional
            } else {
                if (isset($bcc['email'])) {
                    $mail->addBCC($bcc['email'], $bcc['name'] ?? '');               //Name is optional
                } else {
                    foreach ($bcc as $bcc_email) {
                        if (isset($bcc_email['email'])) {
                            $mail->addBCC($bcc_email['email'], $bcc_email['name'] ?? '');               //Name is optional
                        }
                    }
                }
            }
        }

        // 附件
        if ($attachment) {
            if (is_string($attachment)) {
                $mail->addAttachment($attachment);
            } else {
                foreach ($attachment as $attach) {
                    $mail->addAttachment($attach['path'], $attach['name'] ?? '');
                }
            }
        }
        // 内容
        $mail->isHTML(true);                                                                                                                                                                                                                                                                                                                                                                        //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->AltBody = $alt;
        $mail->send();
    }
}