<?php
set_time_limit(1800);
//ini_set('user_agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36');
require_once('simple_html_dom.php');
require_once('connect_DB.php'); // DB = $con

//$url = 'https://www.kmdn.gov.tw/1117/1271/1274/33601';
$url = get_end_url($con);
for ($i = 0; $i < 3000; $i++) {
    ini_set('user_agent',get_rand_useragent());
    $i == 0 ? $p = new Post($url) : $p = new Post($p->nextUrl);
    echo $i . ':' . $p->url;
    echo PHP_EOL;
    try {
        $stmt = $con->prepare('INSERT INTO `kmdn`(`url`,`heading`,`date`,`author`,`clickRate`,`content`) VALUE(:url,:heading,:date,:author,:clickRate,:content)');
        $stmt->bindParam(':url', $p->url);
        $stmt->bindParam(':heading', $p->heading);
        $stmt->bindParam(':date', $p->date);
        $stmt->bindParam(':author', $p->author);
        $stmt->bindParam(':clickRate', $p->clickRate);
        $stmt->bindParam(':content', $p->content);
        $stmt->execute();
        echo $i . ':' . 'executed.';
        echo PHP_EOL;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo 'continue' . PHP_EOL;
            continue;
        } else {
            die("PDOException: " . $e->getMessage() . "<br/>");
        }
    }
    sleep(0.5);
}


class Post
{
    public $url;
    public $heading;
    public $date;
    public $author;
    public $clickRate;
    public $content;
    public $nextUrl;

    protected $find1;

    public function __construct($url)
    {
        $this->url = $url;
        $html = file_get_html($this->url);
        $this->find1 = $html->find('[class=k_con1]', 0);

        $this->getHeading();
        $this->getDate();
        $this->getAuthor();
        $this->getContent();
        $this->getNextUrl();
    }

    public function getHeading()
    {
        $h1 = $this->find1->find('h1', 0);
        //echo strip_tags($h1);
        $this->heading = strip_tags($h1);
    }

    public function getDate()
    {
        $date = $this->find1->find('[class=col-xs-12 date]', 0);
        //echo trim(strip_tags($date));
        $this->date = trim(strip_tags($date));
    }

    public function getAuthor()
    {
        $tmp = $this->find1->find('[class=col-md-10 word]', 0);
        $tmp = trim(strip_tags($tmp));
        preg_match('/作者：(.+?)。.+?點閱率：([0-9]+)/u', $tmp, $matches);
        //echo $matches[1];
        //echo $matches[2];
        $this->author = $matches[1];
        $this->clickRate = $matches[2];
    }

    public function getContent()
    {
        $tmp = $this->find1->find('[class=content]', 0);
        //echo strip_tags($content);
        //$this->content = strip_tags($content);
        preg_match('/<p class="content">(.+?)<\/p>/u', $tmp, $matches);
        $content = html_entity_decode($matches[1]);
        $this->content = $content;
    }

    public function getNextUrl()
    {
        $tmp = $this->find1->find('[class=list_sty]', 1);
        if (preg_match('/<a href="(.+?)">/', $tmp, $matches)) {
            //echo $matches[1];
            $this->nextUrl = 'https://www.kmdn.gov.tw' . $matches[1];
        } else {
            echo $this->url . '<br/>';
            die('Can\'t find nextUrl!');
        }
    }
}

function get_end_url($con){
$sql = "SELECT url FROM `kmdn` ORDER BY id DESC LIMIT 0,1";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
return $result[0]["url"];
}

function get_rand_useragent()
{
    $arr = array(
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.11 TaoBrowser/2.0 Safari/536.11',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.71 Safari/537.1 LBBROWSER',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729;Media Center PC 6.0; .NET4.0C; .NET4.0E; LBBROWSER)',
        'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E; LBBROWSER)',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.84 Safari/535.11 LBBROWSER',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729;Media Center PC 6.0; .NET4.0C; .NET4.0E)',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729;Media Center PC 6.0; .NET4.0C; .NET4.0E; QQBrowser/7.0.3698.400)',
        'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SV1; QQDownload 732; .NET4.0C; .NET4.0E; 360SE)',
        'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729;Media Center PC 6.0; .NET4.0C; .NET4.0E)',
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1',
        'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; QQDownload 732; .NET4.0C; .NET4.0E)',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729;Media Center PC 6.0; .NET4.0C; .NET4.0E)',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729;Media Center PC 6.0; .NET4.0C; .NET4.0E)',
        'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.84 Safari/535.11 SE 2.X MetaSr 1.0',
        'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SV1; QQDownload 732; .NET4.0C; .NET4.0E; SE 2.X MetaSr 1.0)',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:16.0) Gecko/20121026 Firefox/16.0',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:2.0b13pre) Gecko/20110307 Firefox/4.0b13pre',
        'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; zh-CN; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
        'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.133 Safari/534.16',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0)',
        'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
        'Mozilla/5.0 (X11; U; Linux x86_64; zh-CN; rv:1.9.2.10) Gecko/20100922 Ubuntu/10.10 (maverick) Firefox/3.6.10',
        'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.221 Safari/537.36 SE 2.X MetaSr 1.0'
    );
    return $arr[array_rand($arr)];
}
