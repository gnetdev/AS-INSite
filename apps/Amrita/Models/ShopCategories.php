<?php 
namespace Amrita\Models;

class ShopCategories extends \Shop\Models\Categories
{
    /**
     * 
     * @param unknown $category_id
     * @return unknown
     */
    public static function distinctColorTags($category_id)
    {
        $cache = \Cache::instance();
        if (!$cache->exists('distinctColorTags.category.' . $category_id, $result)) 
        {
            $query = array(
                'categories.id' => new \MongoId( (string) $category_id )
            );
            
            // Only include tags where the quantity of all associated variants > 0
            
            $distinct = (new \Shop\Models\Products)->collection()->distinct("variants.tags", $query);
            $distinct = array_values( array_filter( $distinct ) );
            
            $agg = \Shop\Models\Products::collection()->aggregate(array(
                array(
                    '$match' => array('variants.tags' => array( '$in' => $distinct ) )
                ),
                array(
                    '$unwind' => '$variants'
                ),
                array(
                    '$unwind' => '$variants.tags'
                ),
                array(
                    '$group' => array(
                        '_id' => '$variants.tags',
                        'total' => array( '$sum' => '$variants.quantity' ),
                    )
                ),
                array(
                    '$match' => array('total' => array( '$gt' => 0 ) )
                ),
                array(
                    '$project' => array( '_id' => 1 )
                )
            ));
            
            $items = array();
            if (!empty($agg['ok']) && !empty($agg['result']))
            {
                foreach ($agg['result'] as $result)
                {
                    $items[] = $result['_id'];
                }
            }
            
            $result = array();
            foreach ($items as $tag) {
                if (strpos($tag, 'color:') === 0) {
                    $text = trim(str_replace('color:', '', $tag ) );
                    $result[] = array(
                        'text' => $text,
                        'value' => $tag
                    );
                }
            }            
            sort($result);    

            $cache->set('distinctColorTags.category.' . $category_id, $result, 900);
        }
        
        return $result;
    }
    
    /**
     *
     * @param unknown $category_id
     * @return unknown
     */
    public static function distinctSizeTags($category_id)
    {
        $cache = \Cache::instance();
        if (!$cache->exists('distinctSizeTags.category.' . $category_id, $result))
        {
            $query = array(
                'categories.id' => new \MongoId( (string) $category_id )
            );
            
            // Only include tags where the quantity of all associated variants > 0
            
            $distinct = (new \Shop\Models\Products)->collection()->distinct("variants.tags", $query);
            $distinct = array_values( array_filter( $distinct ) );

            $agg = \Shop\Models\Products::collection()->aggregate(array(
                array(
                    '$match' => array('variants.tags' => array( '$in' => $distinct ) )
                ),
                array(
                    '$unwind' => '$variants'
                ),
                array(
                    '$unwind' => '$variants.tags'
                ),
                array(
                    '$group' => array(
                        '_id' => '$variants.tags',
                        'total' => array( '$sum' => '$variants.quantity' ),
                    )
                ),
                array(
                    '$match' => array('total' => array( '$gt' => 0 ) )
                ),
                array(
                    '$project' => array( '_id' => 1 )
                )
            ));
            
            $items = array();
            if (!empty($agg['ok']) && !empty($agg['result']))
            {
                foreach ($agg['result'] as $result)
                {
                    $items[] = $result['_id'];
                }
            }
            
            $result = array();            
            foreach ($items as $tag) {
                if (strpos($tag, 'size:') === 0) {
                    $text = trim(str_replace('size:', '', $tag ) );
                    $result[] = array(
                        'text' => $text,
                        'value' => $tag
                    );
                }
            }            
            sort($result);
            
            $cache->set('distinctSizeTags.category.' . $category_id, $result, 900);            
        }        
    
        return $result;
    }
}