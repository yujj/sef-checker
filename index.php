<?php
if (isset($_REQUEST['url'])) {
	header("Content-type: application/json; charset=utf-8");
    $GLOBALS['headers'] = array();
	$response = checkLink($_REQUEST['url']);
    $response['url'] = urldecode($response['url']);
    $response['orig_url'] = urldecode(urlencodeAsBrowser($_REQUEST['url']));
    $response['http_code'] = $GLOBALS['headers'][0];
	echo json_encode($response);
	exit();
}

function readHeader($ch, $header)
{
    if (preg_match('#^HTTP.*(\d{3}) #', $header, $h)) {
        $GLOBALS['headers'][] = $h[1];
    }
    return strlen($header);
} 
function urlencodeAsBrowser($url){
	include_once('idna_convert.class.php');
	$IDN = new idna_convert();
	$domain = parse_url($url, PHP_URL_HOST);
	$encoded_domain = $IDN->encode($domain);
	$url = str_replace($domain, $encoded_domain, $url);

	$url = str_replace(
		array("%2F", "%3F", "%3D", "%40", "%3A", "%26", "%3B", "%2A", "%27"), 
		array("/",   "?",   "=",   "@",   ":",   "&",   ";",   "*",   "'"  ), 
		urlencode(urldecode($url)));
	return $url;
}

function checkLink($url) {
	$url = urlencodeAsBrowser($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'readHeader'); 
    curl_exec($ch); 
    $response = curl_getinfo($ch);
    curl_close($ch);
    return $response;
}

header("Content-type: text/html; charset=utf-8");
?><html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SEF Checker</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>   
    <script type="text/javascript">
    $(document).ready(function() {
        $("#check").click(function() {
            var original = prepareList($("#original").val());
            $("#original").val(original);
            var sef = prepareList($("#sef").val());
            $("#sef").val(sef);
            var original = original.split(/\r|\r\n|\n/);
            var origCount = original.length;
            var sef = sef.split(/\r|\r\n|\n/);
            var sefCount = sef.length;
            var result = $("#result");
            result.html("");
            if (origCount == sefCount) {
                for (var i=0; i < origCount; i++) {
                    result.append("<tr><td class='number'>"+(i+1)+"</td><td class='url' id='orig"+i+"'><a rel='"+i+"' class='original' href='"+original[i]+"'>"+original[i]+"</a><span class='status'>wating</span></td><td class='url' id='sef"+i+"'><a href='"+sef[i]+"'>"+sef[i]+"</a><span class='status'>wating</span></td><td class='update'><img title='Обновить' onclick='checkRow("+i+", false)' src='update.png'></td></tr>");
                }
            }else {
                alert("Количество адресов в полях должно совпадать");
                return;
            }
            checkRow(0, true);
        })
    });

    function checkRow(i, chekAll) {
        var obj = $("a[rel="+i+"]");
        if (obj == undefined) {
            return;
        }
        $("td#orig"+i+" .status").toggleClass("loading");
        var origRes = null;
        var sefRes = null;
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: {url:obj.attr("href")},
            dataTypr: "json",
            success: function(res) {
                origRes = res;
            },
            complete: function() {
                $("td#orig"+i+" .status, td#sef"+i+" .status").toggleClass("loading");
                $.ajax({
                    type: 'POST',
                    url: 'index.php',
                    data: {url:$("td#sef"+i+" a").attr("href")},
                    success: function(res) {
                        sefRes = res;
                    },
                    complete: function() {
                        $("td#sef"+i+" .status").toggleClass("loading");
                        if (origRes == null || sefRes == null) {
                            $("td#orig"+i+" .status, td#sef"+i+" .status").attr("class", "status").addClass("connectionError error").html("");
                        }else {
                            if (origRes["http_code"] != 301) {
                                if (origRes["http_code"] == null) {
                                    origClass = "notAnswer error";
                                }else {
                                    origClass = "error";
                                }
                            }else if (origRes["url"] != sefRes["orig_url"]) {
                                origClass = "wrongRedirect error";
                            }else if (origRes["redirect_count"] > 1) {
                                origClass = "multiRedirect warning";
                                origRes["http_code"] = "<a target='_blank' title='Проверить через bertal' href='http://www.bertal.ru/index.php?url="+origRes["orig_url"]+"'>"+origRes["http_code"]+"</a>";
                            }else {
                                origClass = "ok";
                            }
                            if (sefRes["http_code"] == 200) {
                                sefClass = "ok";
                            }else {
                                if (sefRes["http_code"] == null) {
                                    sefClass = "notAnswer error";
                                }else {
                                    sefClass = "error";
                                }
                            }
                            $("td#orig"+i+" .status").attr("class", "status").addClass(origClass).html(origRes["http_code"]);
                            $("td#sef"+i+" .status").attr("class", "status").addClass(sefClass).html(sefRes["http_code"]);
                            if (chekAll) {
                                checkRow(i+1, true);
                            }
                        }
                    },
                    error: function(res) {
                        //alert("Ошибка");
                    }
                });
            },
            error: function(res) {
                //alert("Ошибка");
            }
        });
    }

    function prepareList(text) {
        var re = /[\n]{2,}/g;
        text = text.replace(re, "\n");
        re = /[\r]{2,}/g;
        text = text.replace(re, "\r");
        re = /[\r\n]{2,}/g;
        text = text.replace(re, "\r\n");
        re = /^\s+/g;
        text = text.replace(re, "");
        text = text.replace(/\s+$/, "")
        return text;
    }

    </script>
    <style>
        body {
           font: 13px/18px Arial,Helvetica,sans-serif;
           color: #333;
        }
        textarea {
            width: 49%;
            height: 200px;
        }
        #result .status {
            float: right;
            border: 1px solid #aaa;
            background: #bbb;
            padding: 2px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
        }
        #result .error {
            background: #EF4C43;
            border: 1px solid #f20000;
        }
        #result .ok {
            background: #88C329;
            border: 1px solid #06a13d;
        }
        #result .warning {
            background: #6085b9;
            border: 1px solid #3a75c4;
        }
        #result {
            border: 0;
            width: 98%;
        }
        #result .url {
            padding: 2px;
            width: 45%;
            background: #eee;
            vertical-align: middle;
        }
        #result td.number {
            width: 20px;
            text-align: center;
            padding: 2px;
            background: #eee;
            vertical-align: middle;
        }
        #result td.update {
            width: 20px;
            text-align: left;
            padding: 2px;
            vertical-align: middle;
        }
        #result td.update img {
            cursor: pointer;
        }
        .multiRedirect:after {
            content: " | Множественный редирект";
        }
        .wrongRedirect:after {
            content: " | Ошибочный редирект";
        }
        .notAnswer:after {
            content: "Нет ответа";
        }
        .connectionError:after {
            content: "Ошибка подключения"
        }
        #result .loading {
            background-image: url('loading.gif');
            background-position: center;
            background-repeat: no-repeat;
        }
        a {            
            color: #0088CC;
            display: inline-block;
            margin-top: 2px;
            text-decoration: none;
        }
        .status a {
            color: #173797;
            display: inline;
            margin: 0;
        }
    </style>
</head>
<body>
    <div>
        <textarea placeholder="Оригинальные URL'ы" id="original"></textarea>
        <textarea placeholder="ЧПУ" id="sef"></textarea>
    </div>
    <div>
        <button id="check">Проверить</button>
    </div>
    <div><table id="result"></table></div>
</body>
</html>