<?php 
namespace Amrita\ShopReports\BadlyRelatedProducts\Models;

class Posts extends \Dsc\Mongo\Collection 
{
    public $product_id;
    public $product_title;
    public $post_id;
    public $post_title;
    public $reciprocal = 0;
    
    protected $__collection_name = 'report.badlyrelatedproducts.posts';
    
    public function populate()
    {
        static::collection()->remove(array());
        
        $agg = \Shop\Models\Products::collection()->aggregate(array(
            array(
                '$project' => array(
                    'title' => true,
                    'blog.related' => true            
                )
            ),
            array(
                '$unwind' => '$blog.related'
            ),

            array(
                '$project' => array(
                    'product_id' => '$_id',
                    'product_title' => '$title',
                    'post_id' => '$blog.related'
                )
            ),        
        ));        
        
        $items = array();
        if (!empty($agg['ok']) && !empty($agg['result']))
        {
            $items = $agg['result'];
            
            foreach ($items as $key=>$product)
            {
                unset($items[$key]['_id']);
                $items[$key]['reciprocal'] = 0;
                $items[$key]['post_title'] = null;
                
                $result = \Blog\Models\Posts::collection()->findOne(array(
                    '_id' => $product['post_id'],
                    //'shop.products' => $product['product_id'] 
                ));
                if (!empty($result['_id'])) 
                {
                    $items[$key]['post_title'] = $result['title'];
                    if (!empty($result['shop']['products'])) 
                    {
                        if (in_array($product['product_id'], $result['shop']['products'])) 
                        {
                            $items[$key]['reciprocal'] = 1;                            
                        }
                    }
                }
            }
        }
        
        static::collection()->batchInsert($items, array(
            'w' => 1
        ));
        
        static::collection()->remove(array(
            'reciprocal' => 1
        ));
        
        return $this;
    }
    
    public function disassociate()
    {
        \Shop\Models\Products::collection()->update(array(
            '_id' => $this->product_id,
        ), array(
            '$pull' => array(
                'blog.related' => $this->post_id
            )
        ), array(
            'multiple' => false
        ));
        
        return $this->remove();
    }
    
    public static function disassociateAll()
    {
        foreach (static::collection()->find() as $doc) 
        {
            (new static($doc))->disassociate();
        }
        
        return true;
    }
}