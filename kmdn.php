<?php
require_once('connect_DB.php'); // DB = $con
require_once('simple_html_dom.php');

$table = 'test';
$url = 'https://www.kmdn.gov.tw/1117/1271/1274/327158/';
$sql = "CREATE TABLE IF NOT EXISTS `$table`(" .
    "`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT," .
    "`url` TEXT NOT NULL," .
    "`heading` TEXT NOT NULL," .
    "`date` TEXT NOT NULL," .
    "`author` TEXT NOT NULL," .
    "`clickRate` INT NOT NULL," .
    "`content` TEXT NOT NULL" .
    ")CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
$con->query($sql);
for ($i = 0; $i < 3000; $i++) {
    $i === 0 ? $p = new Post($url) : $p = new Post($p->nextUrl);
    // echo nl2br($p);echo $p;
    if ($p->url === 'https://www.kmdn.gov.tw/1117/1271/1274/326373'||$p->date==='2020/12/07') {
        break;
    }
    $sql = "INSERT INTO `$table`(`url`,`heading`,`date`,`author`,`clickRate`,`content`) VALUES(?,?,?,?,?,?);";
    try {
        $stmt = $con->prepare($sql);
        $stmt->execute(array($p->url, $p->heading, $p->date, $p->author, $p->clickRate, $p->content));
    } catch (\PDOException $e) {
        $stmt->debugDumpParams();
        echo $p;
        echo $e->getMessage();
        die("$i");
    }
    sleep(0.8);
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
        $this->find1 = file_get_html($url);

        $this->heading = $this->getHeading();
        $this->date = $this->getDate();
        $this->author = $this->getAuthor();
        $this->clickRate = $this->getClickRate();
        $this->content = $this->getContent();
        $this->nextUrl = $this->getNextUrl();
    }

    public function getHeading()
    {
        return str_replace(' ', '', trim($this->find1->find('h2[class=title]', 0)->plaintext));
    }

    public function getDate()
    {
        return $this->find1->find('time', 0)->plaintext;
    }

    public function getAuthor()
    {
        return trim(str_replace('。', '', $this->find1->find('span[class=times]', 0)->plaintext));
    }
    public function getClickRate()
    {
        return str_replace(',','',trim($this->find1->find('span[class=times]', 1)->plaintext));
    }

    public function getContent()
    {
        return $this->find1->find('meta[name=description]', 0)->content;
    }

    public function getNextUrl()
    {
        return 'https://www.kmdn.gov.tw' . $this->find1->find('a[title=下一則]', 0)->href;
    }

    public function __toString()
    {
        return "Url:$this->url" . PHP_EOL . "heading:$this->heading" . PHP_EOL . "date:$this->date" . PHP_EOL . "author:$this->author" . PHP_EOL . "clickRate:$this->clickRate" . PHP_EOL . "content:$this->content" . PHP_EOL . "nextUrl:$this->nextUrl" . PHP_EOL;
    }
}
