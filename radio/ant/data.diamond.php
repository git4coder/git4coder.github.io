<?php

$cacheFile = 'data.diamond.cache.html';
if(file_exists($cacheFile) && !isset($_GET['update'])){
	echo file_get_contents($cacheFile);
	exit;
}

$maxCol = 15; // 最多显示的列数，后边的列只有少部分产品有数据
date_default_timezone_set('PRC');

// 获取数据
$urls  = array(
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo1_sg.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo2_sgm.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo3_slim.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo4_cr.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo5_nr.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo6_mdhv.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo7_hf.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo7_hf_center.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo9_sc.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo10_z.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_1mobile/ante_mo8_ot.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_3hand/ante_hand1.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_3hand/ante_hand2.html',
	'http://www.diamond-ant.co.jp/english/amateur/antenna/ante_3hand/ante_hand3.html',
);

$prices = array(
  'AZ503'  => 265,
  'AZ504'  => 275,
  'AZ504FX' => 300,
  'AZ505' => 335,
  'AZ505SP'=> 355,
  'AZ506'  => 360,
  'AZ506FX' => 395,
  'AZ506SP'=> 365,
  'AZ506FXH' => 405,
  'AZ507FX' => 420,
  'AZ507RSP'=> 435,
  'AZ507R' => 348,
  'AZ507FXH' => 520,
  'AZ510'  => 400,
  'AZ510FX' => 665,
  'AZ510FMH' => 665,
  'AZ805M' => 345,
  'AZ805N' => 385,
  'AZ805N' => 385,
  'AZ910' => 430,
  'NR770H' => 240,
  'SG7500' => 455,
  'NR770R' => 240,
  'SG7900' => 690,
  'NR770S' => 295,
  'SG7200' => 575,
  'SG7700' => 630,
  'SGM510' => 435,
  'CR77'   => 245,
  'SGM504' => 395,
  'NR770HB'=> 320,
  'SG7400' => 590,
  'NR7700' => 340,
  'SGM506R'=> 520,
  'M150-GSA' => 135,
  'SG9600' => 715,
  'MC101'  => 210,
  'NR770HSP' => 320,
  'NR760R' => 295,
  'M285S'  => 275,
  'MC203A' => 320,
  'SG2000' => 480,
  'MC103A' => 480,
  'DP-CL2E'=> 275,
  'MC101S' => 220,
  'DP-TRY2E' => 300,
  'SG9500M' => 990,
  'SG9700' => 760,
  'NR22L' => 425,
  'NR-77AM' => 495,
  'SG7100R' => 665,
  'CR8900'  => 785,
  'NR770RSP' => 310,
  'NR22L' => 425,
  'MC203B' => 320,
  'SGM505' => 440,
  'SG7000' => 515,
  'SGM507' => 450,
);

$data = array();
foreach ($urls as $url) {
	$data = array_merge($data,getAntData($url));
}

// 设置价格
foreach ($data as $itemKey => $item) {
	foreach ($antAttrType as $key) {
    if('name' == $key){
      $value = isset($item[$key]) ? $item[$key] : '-';
      $values = explode(':', strip_tags($value), 2);
      $name = $values[0];
      if (strpos($name, '/')){
        $keys = explode('/', $name);
        foreach($keys as $k) {
          if(isset($prices[$k])){
            $data[$itemKey]['price'] = $prices[$k];
          }
        }
      }else{
        if(isset($prices[$name])){
          $data[$itemKey]['price'] = $prices[$name];
        }
      }
    }
  }
}

// 字段排序，删除这行数组变量“可以”显示全部列，个数受 $maxCol 的限制
$antAttrType = array(
	'category',
	'name',
  'price', // 这项要放在 name 后边，因为在处理 name 时才为数据设置的 price
	'img',
	'length',
	'gain',
	'max.power rating',
	'type',
	'gain-144MHz',
	'gain-430MHz',
	'impedance',
	'vswr',
	'connector',
	'weight',
	'swr',
	'frequency'
);

$cache = '';

$cache .= "<h1>Diamond Mobile Antennas</h1>\r\n";
$cache .= "<table>\r\n";

$cache .= "\t<thead>\r\n";
$cache .= "\t\t<tr>\r\n";
$i = 0;
foreach ($antAttrType as $th) {
	$i++; if($i > $maxCol) break;
	$cache .= "\t\t\t<th>$th</th>\r\n";
}
$cache .= "\t\t</tr>\r\n";
$cache .= "\t</thead>\r\n";

$cache .= "\t<tbody>\r\n";
foreach ($data as $itemKey => $item) {
	$cache .= "\t\t<tr>\r\n";
	$i = 0;
	foreach ($antAttrType as $key) {
		$i++; if($i > $maxCol) break;
		$value = isset($item[$key]) ? $item[$key] : '-';
    if('name' == $key){
      $values = explode(':', strip_tags($value), 2);
      $title = empty($values[1]) ? $values[0] : $values[1];
      $value = '<span title="' . $title . '">' . $values[0] . '</span>';
    }
		if('img' == $key){$value = '<img src="'.$value.'" height="20" />'; }
		$cache .= "\t\t\t<td>$value</td>\r\n";
	}
	$cache .= "\t\t</tr>\r\n";
}
$cache .= "\t</tbody>\r\n";

$cache .= "</table>\r\n";
$cache .= '<div class="copyright">Data: <a href="http://www.diamond-ant.co.jp/english/amateur/antenna/ama_antennas.html" target="_blank">http://www.diamond-ant.co.jp/english/amateur/antenna/ama_antennas.html</a> ['.date('Y-m-d H:i:s').']</div>'."\r\n";

echo $cache;
file_put_contents($cacheFile, $cache);

function getAntData($url=''){
	global $antAttrType;
	$html = @file_get_contents($url);
	$dir = dirname($url).'/';

	if(!isset($antAttrType)){
		$antAttrType = array('category','name','img');
	}

	// TITLE
	preg_match("@<title>(.*)</title>@smUi",$html, $matches);
	$title    = isset($matches[1]) ? trim($matches[1]) : basename($url);
	$titles   = explode('/',$title); // <title>SLIM GAINER/DIAMOND ANTENNA CORPORATION</title>
	$category = isset($titles[0]) ? $titles[0] : '-';

	// container
	$reg  = '|<div class="ama">(.*?)<p class="up">|s';
	preg_match_all($reg, $html, $matches);
	$body = isset($matches[1][0]) ? $matches[1][0] : '';
	$reg  = '|<div class="product1">(.*?)</div>|s';
	preg_match_all($reg, $body, $matches);
	$products = isset($matches[1]) ? $matches[1] : '';

	$antData = array();

	foreach ($products as $product) {
		// img
		$reg  = '|src="(.*?)"|s';
		preg_match_all($reg, $product, $src);
		$src = isset($src[1][0]) ? $dir.$src[1][0] : '';
		$img = $src;

		// 存到本地
		// $img = basename($src);
		// if(!file_exists($img)){
		// 	if(!file_put_contents($img, file_get_contents($src))){
		// 		$img = $src;
		// 	}
		// }

		// name
		$reg  = '|<p class="pro_tt">(.*?)</p>|s';
		preg_match_all($reg, $product, $name);
		$name = isset($name[1][0]) ? $name[1][0] : '-';
		// attrs
		$reg  = '|<p>(.*?)$|s'; // 部分产品缺少“</p>”,所以不能用这个：$reg  = '|<p>(.*?)</p>|s';
		preg_match_all($reg, $product, $attrs);
		// echo "\r\n\r\n".'=========================================<hr>'.$product;
		// echo "\r\n\r\n".'=========================================<hr>';
		// print_r($attrs);
		// exit;
		$attrs = isset($attrs[1][0]) ? $attrs[1][0] : '-';
		$attrs = str_ireplace('</p>', '', $attrs);
		$attrs = str_ireplace('MHz)Max.power', 'MHz)&nbsp;/&nbsp;Max.power', $attrs);
		$attrs = str_ireplace(' /&nbsp;', '&nbsp;/&nbsp;', $attrs);
		$attrs = str_ireplace('Max.power rating ', 'Max.power rating:', $attrs); // 补分号
		$attrs = preg_split('#(<br />|&nbsp;/&nbsp;|&nbsp;/)#s',$attrs);

		$attributes = array();
		foreach ($attrs as $attr) {
			$attrItem = explode(':', $attr, 2); // 只使用第一个冒号：VSWR:Less than 1.5:1
			if(isset($attrItem[0]) && !empty($attrItem[0])){
				$attrTypeName = $attrItem[0];
				$attrTypeName = strtolower(trim(str_ireplace('&nbsp;','',strip_tags($attrTypeName))));
				if(in_array($attrTypeName, array('connector','coonector','connrctor'))){
					$attrTypeName = 'connector';
				}
				if(in_array($attrTypeName, array('max. power rating','max.power rating'))){
					$attrTypeName = 'max.power rating';
				}
				$attrTypeValue = isset($attrItem[1]) ? str_ireplace(array("\r","\n"),' ',trim($attrItem[1])) : '√';
				// 存值
				$attributes[$attrTypeName] = strip_tags($attrTypeValue);
				// 统计所有类型
				if(!empty($attrTypeName) && !in_array($attrTypeName, $antAttrType)){
					$antAttrType[] = $attrTypeName;
				}
				// gain 再拆
				if('gain' == $attrTypeName){
					$gains = explode(',', $attrTypeValue);
					foreach ($gains as $gain) {
						$gain = trim($gain);
						preg_match("/\((.*?)\)/", $gain, $gainName);
						preg_match("/(^.*?)\(/", $gain, $gainValue);
						$gainName  = isset($gainName[1])  ? $gainName[1]  : '';
						$gainValue = isset($gainValue[1]) ? $gainValue[1] : '';
						$attributes['gain-'.$gainName] = $gainValue;
						if(!empty($gainName) && !in_array('gain-'.$gainName, $antAttrType)){
							$antAttrType[] = 'gain-'.$gainName;
						}
					}
				}
			}
		}

		$antData[] = array_merge(array(
				'category' => '<a href="'.$url.'" target="_blank">'.$category.'</a>',
				'name'     => $name,
				'img'      => $img,
			), $attributes);
	}
	return $antData;
}

// var_dump($antAttrType);
?>
