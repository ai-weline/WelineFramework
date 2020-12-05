<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

/**
 * @DESC         |打印调试
 *
 * @Author       秋枫雁飞
 * @Email        aiweline@qq.com
 * @Forum        https://bbs.aiweline.com
 * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 *
 * 参数区：
 *
 * @param $data
 * @param bool $pass
 * @param int $trace_deep
 */
function p($data = null, bool $pass = false, int $trace_deep = 2): void
{
    // 执行时间
    $exe_time = microtime(true) - START_TIME;

    $parent_call_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $trace_deep);
    $isCli            = (PHP_SAPI === 'cli');
    if (! $isCli) {
        echo '<div style="color: #180808;padding: 2% 5%;border: 2px gray dashed "><pre style="font-size: 20px"><div>';
        print_r("<h3 style=\"color: chocolate\">调试位置：（深度：{$trace_deep}）</h3>");
        echo '<style>body{background-color:#9e9e9e42 }</style>';
    }
    $parent_call_info = array_reverse($parent_call_info);
    foreach ($parent_call_info as $key => $item) {
        if (is_array($item)) {
            foreach ($item as $k => $i) {
                $k     = str_pad($k, 12, '-', STR_PAD_BOTH);
                $i_str = is_string($i) ? $i : json_encode($i);
                ! $isCli ? print_r("<b style='color: dodgerblue'>{$k}</b>  :  <b style='color: darkred'>{$i_str}</b>" . PHP_EOL) : print_r("{$k}   {$i}" . PHP_EOL);
            }
            echo ! $isCli ? '---------------------------------------------------------<br>' : print_r('---------------------------------------------------------' . PHP_EOL);
        } else {
            $key      = str_pad($key, 12, '-', STR_PAD_BOTH);
            $item_str = is_string($item) ? $item : json_encode($item);
            ! $isCli ? print_r("<b style='color: dodgerblue'>{$key}</b>  :  <b style='color: darkred'>{$item_str}</b>" . PHP_EOL) : print_r("{$key}   {$item}" . PHP_EOL);
        }
    }
    ! $isCli ? print_r('<h3 style="color: chocolate">调试信息：</h3><div style="border: #0a464e solid 1px;padding: 2% 2%">') : print_r('调试信息：');
    if (is_object($data)) {
        if (method_exists($data, 'toArray')) {
            $subIsObject = 0;
            foreach ($data->toArray() as $item) {
                if (is_object($item)) {
                    $subIsObject = 1;
                }
            }
            if (! $subIsObject) {
                var_dump(get_class($data));
                echo $isCli ? PHP_EOL : '<br>';
                var_dump($data->toArray());
                echo $isCli ? PHP_EOL : '</div><br><div>调试时间：<br>--' . ($exe_time * 1000) . '(ms/毫秒)<br>--' . $exe_time . '(s/秒)<br></div></div></pre>';
                echo $isCli ? PHP_EOL : '</div></pre>';
                if (! $pass) {
                    die;
                }
            }
        }
        var_dump(get_class($data));
        echo $isCli ? PHP_EOL : '<br>';
        var_dump(get_class_methods($data));
        echo $isCli ? PHP_EOL : '</div><br><div>调试时间：<br>--' . ($exe_time * 1000) . '(ms/毫秒)<br>--' . $exe_time . '(s/秒)<br></div></div></pre>';
        echo $isCli ? PHP_EOL : '</div></div></pre>';
        if (! $pass) {
            die;
        }
    }
    var_dump($data);
    echo $isCli ? PHP_EOL : '</div><br><div>调试时间：<br>--' . ($exe_time * 1000) . '(ms/毫秒)<br>--' . $exe_time . '(s/秒)<br></div></div></pre>';
    if (! $pass) {
        die;
    }
}
