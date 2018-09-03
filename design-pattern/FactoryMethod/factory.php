<?php
abstract class RSSReader{
    protected $xmlReader;
    public function __construct(xmlReader $xmlReader){
        $this->xmlReader = $xmlReader;
    }

    abstract public function display();
}

class xmlReader{
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
    public function __construct(xmlReader $xmlReader){
        parent::__construct($xmlReader);
    }

    public function display(){
        $xml = $this->xmlReader->getXml();
        foreach($xml->item as $item){
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
    public function __construct(xmlReader $xmlReader){
        parent::__construct($xmlReader);
    }

    public function display(){
        $xml = $this->xmlReader->getXml();
        foreach($xml->channel->item as $item){
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
        return isset($this->xml->channel->item);
    }
}

class ATOMReader extends RSSReader{
    public function __construct(xmlReader $xmlReader){
        parent::__construct($xmlReader);
    }

    public function display(){
        $xml = $this->xmlReader->getXml();
        foreach($xml->feed->entry as $entry){
            echo <<<HTML
            <ul>
                <li>{$entry->title}</li>
                <li><a href="{{$entry->link['href']}}">{$entry->link['href']}</a></li>
                <li>{$entry->summary}</li>
            </ul>
HTML;
        }
    }

    public function accept(){
        return isset($this->xml->feed->entry);
    }
}

$url = "https://rss.itmedia.co.jp/rss/1.0/techtarget.xml";
$xmlReader = new xmlReader();
$xmlReader->read($url);

$rssReaders[] = new RSS1Reader($xmlReader);
$rssReaders[] = new RSS2Reader($xmlReader);
$rssReaders[] = new ATOMReader($xmlReader);

foreach($rssReaders as $rss){
    if ($rss->accept()){
        $rss->display();
    }
}