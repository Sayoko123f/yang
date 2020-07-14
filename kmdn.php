<?php
require_once('simple_html_dom.php');
$url = 'https://www.kmdn.gov.tw/1117/1271/1275/321006/';

$p = new Post($url);
//$p->getHeading();
//$p->getDate();
//$p->getAuthor();
//$p->getContent();
$p->getNextUrl();

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
    }

    public function getHeading()
    {
        $h1 = $this->find1->find('h1', 0);
        echo strip_tags($h1);
    }

    public function getDate()
    {
        $date = $this->find1->find('[class=col-xs-12 date]', 0);
        echo trim(strip_tags($date));
    }

    public function getAuthor()
    {
        $tmp = $this->find1->find('[class=col-md-10 word]', 0);
        $tmp = trim(strip_tags($tmp));
        preg_match('/作者：(.+?)。.+?點閱率：([0-9]+)/u', $tmp, $matches);
        echo $matches[1];
        echo $matches[2];
    }

    public function getContent()
    {
        $content = $this->find1->find('[class=content]', 0);
        echo strip_tags($content);
    }

    public function getNextUrl()
    {
        $tmp = $this->find1->find('[class=list_sty]', 1);
        if (preg_match('/<a href="(.+?)">/', $tmp, $matches)) {
            echo $matches[1];
        }
    }
}
