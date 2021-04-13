<?php
$url = trim($_GET['url']);
// 只能从 http://www.diamond-ant.co.jp/ 获取数据，防止用于其它网站
$url = str_replace('http://www.diamond-ant.co.jp/', '', $url);
$url = 'http://www.diamond-ant.co.jp/' . $url;
echo file_get_contents($url);

