<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Ui;

use Weline\Framework\Session\Session;
use Weline\Framework\System\Text;

class FormKey
{
    private Session $_session;
    private string $_key = '';
    private array $_key_paths = [];

    const key_name = 'form_key';
    const form_key_paths = 'form_key_paths';

    function __construct(
        Session $session
    )
    {
        $this->_session = $session;
    }

    function setKey(): static
    {
        if (empty($this->_key)) {
            $this->_key = Text::rand_str();
            $this->_session->setData(self::key_name, $this->_key);
        }
        return $this;
    }

    function __sleep()
    {
        return array();
    }

    function getKey(string $path): string
    {
        if (empty($this->_key)) {
            $this->setKey();
        }
        $this->_key_paths[] = $path;
        # FIXME _key_paths 这个参数可以存储到缓存
        $this->_session->setData(self::form_key_paths, implode(',', $this->_key_paths));
        return $this->_session->getData(self::key_name);
    }

    function getHtml(string $path): string
    {
        return '<input type="hidden" name="form_key" value="' . $this->getKey($path) . '"/>';
    }
}