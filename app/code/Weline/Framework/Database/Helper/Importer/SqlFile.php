<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Helper\Importer;

use Weline\Framework\Database\DbManager;

class SqlFile
{
    //数据库信息
    private string $db_host_name;

    private string $db_user_name;

    private string $db_password;

    private string $db_port;

    private string $db_name;

    private string $db_charset;

    private string $db_table_prefix;

    private $link;

    /**
     * SqlFile constructor.
     * @param DbManager $dbManager
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    public function __construct(DbManager $dbManager)
    {
        $linker             = $dbManager->create()->getConfigProvider();
        $this->db_host_name    = $linker->getHostName();
        $this->db_user_name    = $linker->getUsername();
        $this->db_password     = $linker->getPassword();
        $this->db_port         = $linker->getHostPort();
        $this->db_name         = $linker->getDatabase();
        $this->db_charset      = $linker->getCharset();
        $this->db_table_prefix = $linker->getPrefix();
        $link_info             = $this->link_data();
        if (isset($link_info['status'])) {
            exit($link_info['info']);
        }
    }

    /**
     * @DESC         |连接数据
     *
     * 参数区：
     *
     * @return array|bool
     */
    protected function link_data()
    {
        $link = mysqli_connect($this->db_host_name, $this->db_user_name, $this->db_password, null, $this->db_port);
        if (! $link) {
            return ['status' => false, 'info' => '数据库连接失败'];
        }
        $this->link = $link;
        //mysql 版本
        //获得mysql版本
        $version = mysqli_get_server_info($this->link);
        //设置字符集
        if ($version > '4.1' && $this->db_charset) {
            mysqli_query($link, "SET NAMES {$this->db_charset}");
        }
        //选择数据库
        mysqli_select_db($this->link, $this->db_name);

        return true;
    }

    /**
     * @DESC         |导入数据
     *
     * 参数区：
     *
     * @param string $db_filepath
     * @param string $dbfile_table_pre
     * @return array
     */
    public function import_data(string $db_filepath, string $dbfile_table_pre = 'zq_')
    {
        if (! file_exists($db_filepath)) {
            return ['status' => false, 'info' => '数据库文件不存在'];
        }
        $sql    = file_get_contents($db_filepath);
        $status = $this->_sql_execute($this->link, $sql, $dbfile_table_pre);
        if ($status) {
            return ['status' => true, 'file' => str_replace(APP_PATH, '', $db_filepath), 'info' => '导入数据库成功'];
        }

        return ['status' => true, 'file' => str_replace(APP_PATH, '', $db_filepath), 'info' => '导入数据库失败'];
    }

    /**
     * @DESC         |sql执行
     *
     * 参数区：
     *
     * @param $link
     * @param $sql
     * @param $dbfile_table_pre
     * @return bool
     */
    protected function _sql_execute($link, $sql, $dbfile_table_pre)
    {
        $sqls = $this->_sql_split($link, $sql, $dbfile_table_pre);
        if (is_array($sqls)) {
            foreach ($sqls as $sql) {
                if (trim($sql) !== '') {
                    mysqli_query($link, $sql);
                }
            }
        } else {
            mysqli_query($link, $sqls);
        }

        return true;
    }

    /**
     * @DESC         |sql文件语句拆分
     *
     * 参数区：
     *
     * @param $link
     * @param $sql
     * @param $dbfile_table_pre
     * @return array
     */
    protected function _sql_split($link, $sql, $dbfile_table_pre)
    {
        if (mysqli_get_server_info($link) > '4.1' && $this->db_charset) {
            $sql = preg_replace('/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/', 'ENGINE=\\1 DEFAULT CHARSET=' . $this->db_charset, $sql);
        }
        //如果有表前缀就替换现有的前缀
        if ($this->db_table_prefix) {
            $sql = str_replace($dbfile_table_pre, $this->db_table_prefix, $sql);
        }
        $sql          = str_replace("\r", "\n", $sql);
        $ret          = [];
        $num          = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries   = explode("\n", trim($query));
            $queries   = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 !== '#' && $str1 !== '-') {
                    $ret[$num] .= $query;
                }
            }
            $num++;
        }

        return $ret;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }
}
