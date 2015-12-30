<?php
namespace Home\Model;
use Think\Model\ViewModel;
class ProductBidViewModel extends ViewModel {
    public $viewFields = array(
        'ProductBid'=>array('bid_id','user_id','product_id','bid_price', '_type'=>'LEFT'),
        'Product'=>array('product_name','product_type','product_images','product_type','begin_time','end_time','status','_on'=>'Product.product_id = ProductBid.product_id')
    );

 }
