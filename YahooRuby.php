<?php
/** YahooRuby.php
 * 「Yahoo!JAPAN ルビ振りWebサービス」を利用して、
 *  日本語テキストにルビ（ふりがな）を振る
 *
 * @copyright	(c)studio pahoo
 * @author		パパぱふぅ
 * @動作環境	PHP 4/5/7
 * @参考URL		http://www.pahoo.org/e-soul/webtech/php06/php06-39-01.shtm
 *
 * [コマンドラインで直接呼び出す場合]
 * sentence = 解析するテキスト（UTF-8をURL-encodeしたもの）
 * grade    = 学年 1～8（省略時は 1）
 * （例）YahooRuby.php?sentence=%e9%81%99%e3%81%8b%e5%bd%bc%e6%96%b9%e3%81%ab%e5%b0%8f%e5%bd%a2%e9%a3%9b%e8%a1%8c%e6%a9%9f%e3%81%8c%e8%a6%8b%e3%81%88%e3%82%8b
*/
// 初期化処理 ================================================================
define('INTERNAL_ENCODING', 'UTF-8');
mb_internal_encoding(INTERNAL_ENCODING);
mb_regex_encoding(INTERNAL_ENCODING);
define('MYSELF', basename($_SERVER['SCRIPT_NAME']));
define('REFERENCE', 'http://www.pahoo.org/e-soul/webtech/php06/php06-12-01.shtm');
define('TITLE', 'Yahoo!JAPAN ルビ振りWebサービス');
define('REQUEST_FURIGANA_URL', 'http://jlp.yahooapis.jp/FuriganaService/V1/furigana');
define('SAMPLE_TEXT', '長徳元年（995年）、兄の中関白・藤原道隆が他界すると、藤原道長が内覧となり、実質的に政権を掌握した。長保元年（999年）11月1日、彰子は8歳年上の従兄・一条天皇に入内する。');

//リリース・フラグ（公開時にはTRUEにすること）
define('FLAG_RELEASE', FALSE);

//Yahoo! JAPAN Webサービス アプリケーションID
//http://help.yahoo.co.jp/help/jp/developer/developer-06.html にて登録のこと．
define('APPLICATION_ID', 'dj00aiZpPU5rNFZXc1ExN1c1WiZzPWNvbnN1bWVyc2VjcmV0Jng9NTE-');

/**
 * 共通HTMLヘッダ
 * @global string $HtmlHeader
*/
$encode = INTERNAL_ENCODING;
$title = TITLE;
$HtmlHeader =<<< EOT
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="{$encode}">
<title>{$title}</title>
<meta name="author" content="studio pahoo" />
<meta name="copyright" content="studio pahoo" />
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
</head>

EOT;

/**
 * 共通HTMLフッタ
 * @global string $HtmlFooter
*/
$HtmlFooter =<<< EOT
</html>

EOT;

// サブルーチン ==============================================================
/**
 * エラー処理ハンドラ
*/
function myErrorHandler ($errno, $errmsg, $filename, $linenum, $vars) {
	echo 'Sory, system error occured !';
	exit(1);
}
error_reporting(E_ALL);
if (FLAG_RELEASE)	$old_error_handler = set_error_handler('myErrorHandler');

/**
 * PHP5以上かどうか検査する
 * @return	bool TRUE：PHP5以上／FALSE:PHP5未満
*/
function isphp5over() {
	$version = explode('.', phpversion());

	return $version[0] >= 5 ? TRUE : FALSE;
}

/**
 * 指定したパラメータを取り出す
 * @param	string $key  パラメータ名（省略不可）
 * @param	bool   $auto TRUE＝自動コード変換あり／FALSE＝なし（省略時：TRUE）
 * @param	mixed  $def  初期値（省略時：空文字）
 * @return	string パラメータ／NULL＝パラメータ無し
*/
function getParam($key, $auto=TRUE, $def='') {
	if (isset($_GET[$key]))		$param = $_GET[$key];
	else if (isset($_POST[$key]))	$param = $_POST[$key];
	else							$param = $def;
	if ($auto)	$param = mb_convert_encoding($param, INTERNAL_ENCODING, 'auto');
	return $param;
}

/**
 * 指定したパラメータを取り出す（整数バリデーション付き）
 * @param	string $key  パラメータ名（省略不可）
 * @param	int    $def  デフォルト値（省略可）
 * @param	int    $min  最小値（省略可）
 * @param	int    $max  最大値（省略可）
 * @return	int 値／FALSE
*/
function getParam_validateInt($key, $def='', $min=0, $max=9999) {
	//パラメータの存在チェック
	if (isset($_GET[$key]))			$param = $_GET[$key];
	else if (isset($_POST[$key]))	$param = $_POST[$key];
	else							$param = $def;
	//整数チェック
	if (preg_match('/^[0-9\-]+$/', $param) == 0)	return FALSE;
	//最小値・最大値チェック
	if ($param < $min || $param > $max)				return FALSE;

	return $param;
}

/**
 * 指定したパラメータを取り出す（文字列バリデーション付き）
 * @param	string $key  パラメータ名（省略不可）
 * @param	bool   $auto TRUE＝自動コード変換あり／FALSE＝なし（省略時：TRUE）
 * @param	int    $def  デフォルト値（省略可）
 * @param	int    $min  文字列長・最短（省略可）
 * @param	int    $max  文字列長・最長（省略可）
 * @return	string 文字列／FALSE
*/
function getParam_validateStr($key, $auto=TRUE, $def='', $min=3, $max=80) {
	//パラメータの存在チェック
	if (isset($_GET[$key]))			$param = $_GET[$key];
	else if (isset($_POST[$key]))	$param = $_POST[$key];
	else							$param = $def;
	if ($auto)	$param = mb_convert_encoding($param, INTERNAL_ENCODING, 'auto');
	$param = htmlspecialchars(strip_tags($param));		//タグを除く
	//文字列長チェック
	$len = mb_strlen($param);
	if ($len < $min || $len > $max)		return FALSE;

	return $param;
}

/**
 * HTTP通信を行う
 * @param	string $url "http://" から始まるURL
 * @param	string $method GET,POST,HEAD (省略時はGET)
 * @param	string $headers その他の任意のヘッダ (省略時は"")
 * @param	array  $post POST変数を格納した連想配列("変数名"=>"値") (省略時はNULL)
 * @param	string $cookie Cookie(利用するときは常に$method="POST") (省略時は"")
 * @return	string 取得したコンテンツ／FALSE 取得エラー
*/
function http($url, $method='GET', $headers='', $post=NULL, $cookie='') {
	if ($cookie != '')	$method = 'POST';
	$URL = parse_url($url);

	$URL['query'] = isset($URL['query']) ? $URL['query'] : '';		//クエリ
	$URL['port']  = isset($URL['port'])  ? $URL['port']  : 80;		//ポート番号

	//リクエストライン
	$request  = $method . ' ' . $URL['path'] . $URL['query'] . " HTTP/1.1\r\n";

	//リクエストヘッダ
	$request .= 'Host: ' . $URL['host'] . "\r\n";
	$request .= 'User-Agent: PHP/' . phpversion() . "\r\n";

	//Basic認証用のヘッダ
	if (isset($URL['user']) && isset($URL['pass'])) {
		$request .= 'Authorization: Basic ' . base64_encode($URL['user'] . ':'. $URL['pass']) . "\r\n";
	}

	//追加ヘッダ
	$request .= $headers;

	//POSTの時
	if (strtoupper($method) == 'POST') {
		while (list($name, $value) = each($post)) {
			$POST[] = $name . '=' . $value;
		}
		$postdata = implode('&', $POST);
		$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$request .= 'Content-Length: ' . strlen($postdata) . "\r\n";
		if ($cookie != '')	$request .= "Cookie: $cookie\r\n";
		$request .= "\r\n";
		$request .= $postdata;
	} else {
		$request .= "\r\n";
	}

	//接続
	$fp = fsockopen($URL['host'], $URL['port']);
	//エラー処理
	if (!$fp)	return FALSE;

	//リクエスト送信
	fputs($fp, $request);

	//応答データ受信
	$flag = FALSE;
	while (! feof($fp)) {
		$s = trim(fgets($fp));
		if (preg_match('/^\<\?xml/', $s, $arr) != 0) {
			$response = $s;
			$flag = TRUE;
		} else if ($flag && preg_match('/^[0-9|a-f]+$/iu', $s) == 0) {
			$response .= $s;
		}
	}
    fclose($fp);

	return $response;
}

/**
 * 指定XMLファイルを読み込んでDOMを返す
 * @param	string $xml XMLファイル名
 * @return	object DOMオブジェクト／NULL 失敗
*/
function read_xml($xml) {
	if (isphp5over())	return NULL;
	if (($fp = fopen($xml, 'r')) == FALSE)	return NULL;

	//いったん変数に読み込む
	$str = fgets($fp);
	$str = preg_replace('/UTF-8/', 'utf-8', $str);

	while (! feof($fp)) {
		$str = $str . fgets($fp);
	}
	fclose($fp);

	//DOMを返す
	$dom = domxml_open_mem($str);
	if ($dom == NULL) {
		echo "\n>Error while parsing the document - " . $xml . "\n";
		exit(1);
	}

	return $dom;
}

/**
 * 「Yahoo!JAPAN ルビ振りWebサービス」を用いてルビを振る
 * @param	string $sentence ルビを振るテキスト
 * @param	int    $grade 学年（1～8）
 * @param	array  $items    ルビ振り結果を格納する配列
 * @return	bool TRUE/FALSE
*/
function getRuby($sentence, $grade, &$items) {
//WebAPIにパラメータをPOST渡しする
	$url = REQUEST_FURIGANA_URL;
	$sentence = urlencode($sentence);
	$post = array(
		'appid'        => APPLICATION_ID,
		'grade'        => $grade,
		'sentence'     => $sentence
	);

	$res = @http($url, 'POST', '', $post);
	if ($res == FALSE)	return FALSE;

	$i = 0;
//PHP4用; DOM XML利用
	if (isphp5over() == FALSE) {
		$dom = @domxml_open_mem($res);
		if ($dom == FALSE)	return FALSE;
		//ルビ
		if (($ResultSet = $dom->get_elements_by_tagname('ResultSet')) == NULL)	return FALSE;
		$Result = $ResultSet[0]->get_elements_by_tagname('Result');
		foreach ($Result as $val1) {
			$WordList = $val1->get_elements_by_tagname('WordList');
			foreach ($WordList as $val2) {
				$Word = $val2->get_elements_by_tagname('Word');
				foreach ($Word as $val3) {
					if (($node = $val3->get_elements_by_tagname('SubWordList')) != NULL) {
						$SubWord = $node[0]->get_elements_by_tagname('SubWord');
						foreach ($SubWord as $val4) {
							$node1 = $val4->get_elements_by_tagname('Surface');
							$items[$i]['surface'] = $node1[0]->get_content();
							if (($node1 = $val4->get_elements_by_tagname('Furigana')) != NULL) {
								$items[$i]['furigana'] = $node1[0]->get_content();
							} else {
								$items[$i]['furigana'] = $items[$i]['surface'];
							}
							$i++;
						}
					} else {
						$node2 = $val3->get_elements_by_tagname('Surface');
						$items[$i]['surface'] = $node2[0]->get_content();
						if (($node2 = $val3->get_elements_by_tagname('Furigana')) != NULL) {
							$items[$i]['furigana'] = $node2[0]->get_content();
						} else {
							$items[$i]['furigana'] = $items[$i]['surface'];
						}
						$i++;
					}
				}
			}
		}

//PHP5用; SimpleXML利用
	} else {
		$ResultSet = simplexml_load_string($res);
		if (! isset($ResultSet->Result->WordList))	return FALSE;
		//ルビ
		foreach ($ResultSet->Result->WordList as $WordList) {
			foreach ($WordList->Word as $Word) {
				if (isset($Word->SubWordList)) {
					foreach ($Word->SubWordList->SubWord as $SubWord) {
						$items[$i]['surface'] = (string)$SubWord->Surface;
						if (isset($SubWord->Furigana)) {
							$items[$i]['furigana'] = (string)$SubWord->Furigana;
						} else {
							$items[$i]['furigana'] = $items[$i]['surface'];
						}
						$i++;
					}
				} else {
					$items[$i]['surface'] = (string)$Word->Surface;
					if (isset($Word->Furigana)) {
						$items[$i]['furigana'] = (string)$Word->Furigana;
					} else {
						$items[$i]['furigana'] = $items[$i]['surface'];
					}
					$i++;
				}
			}
		}
	}

	return TRUE;
}


/**
 * ルビ振り結果をテキストに反映する
 * @param	array  $items  ルビを格納した配列
 * @return	string ルビ振り結果
*/
function setRuby($items) {
	$outstr = '';

	foreach ($items as $val) {
		if ($val['surface'] != $val['furigana']) {
			$outstr .=<<< EOT

<ruby>
<rb>{$val['surface']}</rb>
<rp>(</rt>
<rt>{$val['furigana']}</rt>
<rp>)</rt>
</ruby>

EOT;
		} else {
			$outstr .= $val['surface'];
		}
	}

	return $outstr;
}

/**
 * HTML BODYを作成する
 * @param	string $sentence 校正原文
 * @param	string $items    情報配列
 * @param	string $errmsg   エラーメッセージ
 * @param	string $url      WebAPI URL
 * @return	string HTML BODY
*/
function makeCommonBody($sentence, $items, $errmsg, $url) {
	$myself = MYSELF;
	$refere = REFERENCE;
	$flag_release = FLAG_RELEASE;

	$p_title = TITLE;
	$version = '<span style="font-size:small;">' . date('Y/m/d版', filemtime(__FILE__)) . '</span>';

	if ($errmsg != '') {
		$errmsg = '<span style="color:red;">error: ' . $errmsg . '</span>';
	}

	if (! $flag_release) {
		$phpver = phpversion();
		if (! isphp5over()) {
			$enable = 'DOM XML : ';
			$enable .= function_exists('domxml_open_mem') ? 'enabled' : 'disable';
		} else {
			$enable = 'SimpleXML : ';
			$enable .= function_exists('simplexml_load_file') ? 'enabled' : 'disable';
		}
		$phpver =<<< EOT
PHPver : {$phpver}<br />
{$enable}<br />
WebAPI : <a href="{$url}">{$url}</a><br />
<dl>

EOT;
	} else {
		$phpver = '';
	}

	$outstr = setRuby($items);		//ルビ振り

	$body =<<< EOT
<body>
<h2>{$p_title} {$version}</h2>
<form name="myform" method="POST" action="{$myself}" enctype="multipart/form-data">
<table>
<tr>
<td>元のテキスト</td>
<td>&nbsp;</td>
<td>ルビ振り</td>
</tr>
<tr>
<td><textarea name="sentence" id="sentence" rows="10" cols="30">{$sentence}</textarea></td>
<td>⇒</td>
<td style="width:300px;">{$outstr}</td>
</tr>
<tr>
<td>
<input type="submit" name="exec" value="ルビ" />　
<input type="submit" name="reset" value="リセット" />
</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>
</form>
{$errmsg}
<div style="border-style:solid; border-width:1px; margin:20px 0px 0px 0px; padding:5px; width:500px; font-size:small;">
<h3>使い方</h3>
<ol>
<li>［<span style="font-weight:bold;">元のテキスト</span>］にルビを振りたいテキストを入力してください。</li>
<li>［<span style="font-weight:bold;">ルビ</span>］ボタンを押してください。</li>
<li>ルビ振りテキストが表示されます。</li>
<li>［<span style="font-weight:bold;">リセット</span>］ボタンを押すと、表示がクリアされます。</li>
</ol>
※参考サイト：<a href="{$refere}">{$refere}</a>
<p>{$phpver}</p>
</div>
</body>

EOT;
	return $body;
}

// メイン・プログラム =======================================================
$items = array();
$errmsg = '';
$outstr = '';
$sentence = getParam('sentence', TRUE, SAMPLE_TEXT);
$grade    = '8';
if ($grade == FALSE)	$errmsg = '学年は 1～8 の整数で指定してください．';
if ($errmsg == '' && $sentence != '') {
	$res = getRuby($sentence, $grade, $items);
	$errmsg = ($res == FALSE) ? 'WebAPIが停止しています．' : '';
}

$HtmlBody = makeCommonBody($sentence, $items, $errmsg, REQUEST_FURIGANA_URL);

// 表示処理
echo $HtmlHeader;
echo $HtmlBody;
echo $HtmlFooter;

/*
** バージョンアップ履歴 ===================================================
 *
 * @version  2.1  2017/04/30  PHP7 対応
 * @version  2.0  2014/07/27  大幅改訂
 * @version  1.0  2008/07/20
*/
?>
