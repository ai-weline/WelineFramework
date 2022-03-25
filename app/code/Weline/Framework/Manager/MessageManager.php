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

    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    public function addError(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'error'));
        $this->session->setData('has-error', '1');
        return $this;
    }

    public function hasErrorMessage(): bool
    {
        return (bool)$this->session->getData('has-error');
    }

    public function addSuccess(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'success'));
        $this->session->setData('has-success', '1');
        return $this;
    }

    public function hasSuccessMessage(): bool
    {
        return (bool)$this->session->getData('has-success');
    }

    public function addWarning(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'warning'));
        $this->session->setData('has-warning', '1');
        return $this;
    }

    public function hasWarningMessage(): bool
    {
        return (bool)$this->session->getData('has-warning');
    }

    public function addNotes(string $msg)
    {
        $this->session->addData('system-message', $this->processMessage($msg, 'notes'));
        $this->session->setData('has-notes', '1');
        return $this;
    }

    public function hasNotesMessage(): bool
    {
        return (bool)$this->session->getData('has-notes');
    }

    public function render(): string
    {
        $html = "<div class='system message'>{$this->session->getData('system-message')}</div>";
        $this->session->delete('system-message');
        return $html;
    }

    public function processMessage(string $msg, string $html_class = 'error'): string
    {
        return "<div class='$html_class'>$msg</div>";
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
