<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/9
 * 时间：23:23
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
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
function p($data, bool $pass = false, int $trace_deep = 2): void
{

    $parent_call_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS , $trace_deep)[1];
    $isCli = (PHP_SAPI == 'cli');
    if (!$isCli) {
        print_r("<h3 style=\"color: chocolate\">调试位置：（深度：{$trace_deep}）</h3>");
        echo '<style>body{background-color:#252729 }</style>';
        echo '<div style="color: #cbc6c6 "><pre style="font-size: 25px">';
    }
    foreach ($parent_call_info as $key => $item) {
        $key = str_pad($key, 12, '-', STR_PAD_BOTH);
        $item_str = is_string($item)?$item:json_encode($item);
        !$isCli ? print_r("<b style='color: dodgerblue'>{$key}</b>  :  <b style='color: darkred'>{$item_str}</b>" . PHP_EOL) : print_r("{$key}   {$item}". PHP_EOL);
    }
    !$isCli ? print_r('<h3 style="color: chocolate">调试信息：</h3>') : print_r('调试信息：');
    if (is_object($data)) {
        if (method_exists($data, 'toArray')) {
            $subIsObject = 0;
            foreach ($data->toArray() as $item) {
                if (is_object($item)) $subIsObject = 1;
            }
            if (!$subIsObject) {
                var_dump(get_class($data));
                echo $isCli ? PHP_EOL : '<br>';
                var_dump($data->toArray());
                echo $isCli ? PHP_EOL : '</div></pre>';
                die;
            }
        }
        var_dump(get_class($data));
        echo $isCli ? PHP_EOL : '<br>';
        var_dump(get_class_methods($data));
        echo $isCli ? PHP_EOL : '</div></pre>';
        die;
    }
    if ($pass) {
        var_dump($data);
        echo $isCli ? PHP_EOL : '</div></pre>';
    } else {
        var_dump($data);
        echo $isCli ? PHP_EOL : '</div></pre>';
        die;
    }
}

/**
 * @DESC         |翻译
 *
 * 参数区：
 *
 * @param $words
 * @return string
 * @throws @\M\Framework\App\Exception
 */
function __(string $words)
{
    $filename = \M\Framework\App\Env::path_TRANSLATE_WORDS_FILE;
    if (!file_exists($filename)) {
        touch($filename);
    }
    try {
        $all_words = (array)include $filename;
    }catch (\M\Framework\App\Exception $exception){
        throw new \M\Framework\App\Exception($exception->getMessage());
    }
    $all_words[] = $words;
    $file = fopen($filename, 'w+');
    $text = '<?php return ' . var_export($all_words, true) . ';';
    fwrite($file, $text);
    fclose($file);
    return $words;
}

/**
 * @DESC         |以大写字母分割字符串
 *
 * 参数区：
 *
 * @param string $str
 * @return array|false|string[]
 */
function m_split_by_capital(string $str)
{
    return preg_split('/(?=[A-Z])/', $str);
}