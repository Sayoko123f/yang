<!doctype html>
<html>
<head>
<meta charset="utf8">
</head>
<body>
<?php
ini_set('memory_limit','500M');
set_time_limit(3000);
$time_start = microtime(true);
require_once('simple_html_dom.php');
//https://twblg.dict.edu.tw/holodict_new/audio/52186.mp3;
//https://twblg.dict.edu.tw/holodict_new/result_detail.jsp?n_no=54800&curpage=1&sample=%E7%BE%8E%E6%96%B9&radiobutton=1&querytarget=1&limit=20&pagenum=1&rowcount=1;
/*$datas = getDatas();
$json_string = twoDimenArraytoJson($datas);
$file = fopen("result3.json","w");
fwrite($file,$json_string);
fclose($file);*/
for($i=50051;$i<50500;++$i){
    $url = "https://twblg.dict.edu.tw/holodict_new/result_detail.jsp?n_no="."$i"."&curpage=1&sample=%E7%BE%8E%E6%96%B9&radiobutton=1&querytarget=1&limit=20&pagenum=1&rowcount=1";
    $html = file_get_html($url);
    $tab = $html->find('[class=shengmuTab]',0);
    if($tab == null){continue;}
    $word = plaintext($tab->find('[class=all_space3]',0));
    $eng = plaintext($tab->find('[class=all_space3]',1));
    echo $i.$word.$eng."<br/>";
}
function get50001(){
    ;
}

function getCimuNo(){//return 2-dimen array
    $data = [];$id = 1;
    foreach(getCimuLink() as $link){
        foreach (getCimu($link) as $pack){
            if($pack == []){continue;}
            $pack['Cid'] = $id;
            $data[] = $pack;
            ++$id;
        }
    }
    return $data;
}

function getCimuLink(){//return array,cimu URL
    $links = [];
    for($i=0;$i<23;++$i){
        $url_level2 = "https://twblg.dict.edu.tw/holodict_new/index/cimu_level2.jsp?level1=".($i+1);
        $html = file_get_html($url_level2);
        $tab = $html->find('[class=cimuTab]',0);
        preg_match_all("/href=\"(.+?)\">/",$tab,$matches);
        foreach($matches[1] as $link){
            $url_level4 = "https://twblg.dict.edu.tw/holodict_new/index/".$link;
            $links[] = $url_level4;
        }
    }
    return $links;
}
function getCimu($url_level4){
    $html = file_get_html($url_level4);
    $space2 = $html->find('[class=cimuTab]',0);
    $a = getNos($space2);
    return $a;
}

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<br/>".$time;


function getDatas(){//return 2-dimen array
    $shengmu = ['p','ph','b','m','t','th','n','l','k','kh','g','ng','h','ts','tsh','s','j','%E9%9B%B6%E8%81%B2%E6%AF%8D'];//零聲母
    $in_idx = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18');
    $id = 2681;
    $data = [];
    for($i=17;$i<18;++$i){//$i<18
        $s = $shengmu[$i];
        $url_level2 = "https://twblg.dict.edu.tw/holodict_new/index/shengmu_level2.jsp?shengmu=".$s;
        $yunmus = matchYunmus($url_level2);
        foreach($yunmus as $y){
            $url_level3 = "https://twblg.dict.edu.tw/holodict_new/index/shengmu_level3.jsp?shengmu=".$s."&yunmu=".$y;
            //echo $url_level3."<br/>";
            $shengdiaos = matchShengdiao($url_level3);
            foreach($shengdiaos as $d){
                $url_level4 = "https://twblg.dict.edu.tw/holodict_new/index/shengmu_level4.jsp?shengmu=".$s."&yunmu=".$y."&shengdiao=".$shengdiaos[0]."&in_idx=".$in_idx[$i].$y.$d;
                echo $url_level4."<br/>";
                $html_level4 = file_get_html($url_level4);
                $space1 = $html_level4->find('[class=all_space1]',0);
                /*$pro = matchPro($space1);
                foreach (matchWords($space1) as $v){
                    //echo $v." ".$s.$y.$d."<br/>";
                    if($i != 17){$data[] = array('id'=>$id,'word'=>$v,'pro'=>"$pro",'shengmu'=>"$s",'yunmu'=>"$y",'diao'=>"$d");}
                    else        {$data[] = array('id'=>$id,'word'=>$v,'pro'=>"$pro",'shengmu'=>"零聲母",'yunmu'=>"$y",'diao'=>"$d");}
                    $datas[] = $data;
                    ++$id;
                }*/
                foreach (getNos($space1) as $pack){
                    if($pack == []){continue;}
                    if($i == 17){$pack['id'] = $id; $pack['pro_diao'] = $y.$d;}
                    else{$pack['id'] = $id; $pack['pro_diao'] = $s.$y.$d;}
                    $data[] = $pack;
                    ++$id;
                }
            }
        }echo "finish $i page"."<br/>";
    }
    return $data;
}

function getNos($space1){//return array
    $pack = [];
    if(preg_match("/<a.+<\/a>/",$space1)==1){
        preg_match_all("/<a.+?<\/a>/",$space1,$matches);
        $i = 0;
        foreach($matches[0] as $v){
            preg_match_all("/n_no=\d+/",$v,$nos);
            if(isset($nos[0][0]) == 0){echo "no為空";continue;}
            $a = preg_replace("/n_no=/","",$nos[0][0]);
            $str = plaintext($v);

            $exit = preg_match_all("/\p{Han}+/u",$str,$words);if ($exit==0){return $pack;}
            $word = $words[0][0];
            //echo $word;
            //$eng = str_replace($word,"",$str);
            //echo $eng."<br/>";
            $indu = plaintext($space1->find('[class=tlsound]',$i));
            //echo $indu."<br/>";
            $tmp = array('no'=>$a,'word'=>$word,'pro'=>$indu);
            $pack[] = $tmp;
            ++$i;
        }
    }
    return $pack;
}

function matchShengdiao($url_level3){//return array
    $html = file_get_html($url_level3);
    $table = $html->find('[class=shengmuTab]',0);
    preg_match_all("/shengdiao=[1-9]/",$table,$matches);
    $tmp = $matches[0];
    $a = [];
    foreach ($tmp as $v){
        preg_match_all("/[1-8]/",$v,$matches);
        $a[] = $matches[0][0];
    }
    return $a;
}

function matchYunmus($url_level2){//return array
    $html = file_get_html($url_level2);
    $table = $html->find('[class=shengmuTab]',0);
    $yunmus = preg_replace("/<.{1,100}?>/","",$table->find('a'));
    return $yunmus;
}

function matchWords($space1){//return array
    //$html = file_get_html($url_level4);
    //$space1 = $html->find('[class=all_space1]',0);
    preg_match_all("/\p{Han}+/u",$space1,$matches);
    $a = $matches[0];
    array_shift($a);
    return $a;
}
function twoDimenArraytoJson($datas){//return json string
        $a = [];
    foreach($datas as $data){
        foreach($data as $key =>$value){
            $new_array[urlencode($key)] = urlencode($value);
        }
        $a[] = $new_array;
    }
    $b = urldecode(json_encode($a));
    return $b;
}
function matchPro($space1){
    //$html = file_get_html($url_level4);
    //$space1 = $html->find('[class=all_space1]',0);
    $pro = $space1->find('[class=tlsound]',0);
    return plaintext($pro);
}
function plaintext($str){//提取字串$str純文字
    $str = trim($str);
    $str = preg_replace("/\t/","",$str);
    $str = preg_replace("/\r\n/","",$str);
    $str = preg_replace("/\n/","",$str);
    $str = preg_replace("/ /","",$str);
    $str = preg_replace("/　/","",$str);
    $str = preg_replace("/<.+?>/","",$str,10);//移除<>標籤
    return trim($str);
}
?>

</body>
</html>