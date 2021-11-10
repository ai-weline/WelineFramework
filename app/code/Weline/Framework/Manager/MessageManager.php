<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Manager;

use Weline\Framework\Session\Session;

class MessageManager
{
    private Session $session;

    function __construct(
        Session $session
    )
    {
        $this->session = $session;
    }

    function addError(string $msg)
    {

        $this->session->addData('system-message', $this->processMessage($msg, 'error'));
        return $this;
    }

    function addSuccess(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'success'));
        return $this;
    }

    function addWarning(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'warning'));
        return $this;
    }

    function addNotes(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'notes'));
        return $this;
    }

    function render(): string
    {
        $html = "<div class='system message'>{$this->session->getData('system-message')}</div>";
        $this->session->delete('system-message');
        return $html;
    }

    function processMessage(string $msg, string $html_class = 'error'): string
    {
        return "<div class='$html_class'>$msg</div>";
    }
}