<?php
class Order{
    private $customer;
    public function __construct($customer){
        $this->customer = $customer;
    }
    
    public function getCustomer(){
        return $this->customer;
    }
    
    public function setCustomer($customer){
        $this->customer = $customer;
    }
}

class OrdersIterator implements IteratorAggregate
{
    protected $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayObject(); 
    }
    
    public function add($attribute){
        $this->attributes[] = $attribute; 
    }

    public function getIterator(){
        return $this->attributes->getIterator();
    }
}

$ordersIterator = new OrdersIterator();
$ordersIterator->add(new Order("田中一郎"));
$ordersIterator->add(new Order("山田太郎"));

foreach($ordersIterator as $iterator){
    var_dump($iterator->getCustomer());
}