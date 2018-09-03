<?php
ini_set("display_errors", 1);
error_reporting(-1);

abstract class RSSReader{
    protected $xml;
    public function __construct(xml $xmlReader){
        $this->xml = $xmlReader->getXml();
    }

    abstract public function display();
}

class xml{
    private $xml;
    public function read($url){
        $this->xml = simplexml_load_file($url);
        if (!$this->xml){
            throw new Exception("file is not readable");
        }
    }

    public function getXml(){
        return $this->xml;
    }
}

class RSS1Reader extends RSSReader{
    public function __construct(xml $xmlReader){
        parent::__construct($xmlReader);
    }

    public function display(){
        foreach($this->xml->item as $item){
            echo <<<HTML
            <ul>
                <li>{$item->title}</li>
                <li><a href="{{$item->link}}">{$item->link}</a></li>
                <li>{$item->description}</li>
            </ul>
HTML;
        }
    }

    public function accept(){
        return isset($this->xml->item);
    }
}

class RSS2Reader extends RSSReader{
    public function __construct(xml $xmlReader){
        parent::__construct($xmlReader);
    }

    public function display(){
        foreach($this->xml->channel->item as $item){
            echo <<<HTML
            <ul>
                <li>{$item->title}</li>
                <li><a href="{$item->link}">{$item->link}</a></li>
                <li>{$item->description}</li>
            </ul>
HTML;
        }
    }

    public function accept(){
        return isset($this->xml->channel->item);
    }
}

class ATOMReader extends RSSReader{
    public function __construct(xml $xmlReader){
        parent::__construct($xmlReader);
    }

    public function display(){
        foreach($this->xml->entry as $entry){
            echo <<<HTML
            <ul>
                <li>{$entry->id}</li>
                <li><a href="{$entry->link['href']}">{$entry->link['href']}</a></li>
                <li>{$entry->content}</li>
            </ul>
HTML;
        }
    }

    public function accept(){
        return isset($this->xml->entry);
    }
}

$rssurl = [
    "https://rss.itmedia.co.jp/rss/1.0/techtarget.xml",
    "http://ascii.jp/rss.xml",
    "https://github.com/fog/fog-sakuracloud/wiki.atom"
];

foreach($rssurl as $url){
    $xmlReader = new xml();
    $xmlReader->read($url);

    $rssReaders   = [];
    $rssReaders[] = new RSS1Reader($xmlReader);
    $rssReaders[] = new RSS2Reader($xmlReader);
    $rssReaders[] = new ATOMReader($xmlReader);

    foreach($rssReaders as $rss){
        if ($rss->accept()){
            $rss->display();
            break;
        }
    }
}