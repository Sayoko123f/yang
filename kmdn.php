<?php
set_time_limit(1800);
ini_set('user_agent','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36');
require_once('simple_html_dom.php');
require_once('connect_DB.php'); // DB = $con


$url = 'https://www.kmdn.gov.tw/1117/1271/1274/292726';
for ($i = 0; $i < 2000; $i++) {
    $i == 0 ? $p = new Post($url) : $p = new Post($p->nextUrl);
    //echo $p->url . '<br/>';
    try {
        $stmt = $con->prepare('INSERT INTO `kmdn`(`url`,`heading`,`date`,`author`,`clickRate`,`content`) VALUE(:url,:heading,:date,:author,:clickRate,:content)');
        $stmt->bindParam(':url', $p->url);
        $stmt->bindParam(':heading', $p->heading);
        $stmt->bindParam(':date', $p->date);
        $stmt->bindParam(':author', $p->author);
        $stmt->bindParam(':clickRate', $p->clickRate);
        $stmt->bindParam(':content', $p->content);
        $stmt->execute();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
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
