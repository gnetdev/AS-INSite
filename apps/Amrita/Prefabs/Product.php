<?php 
namespace Amrita\Prefabs;

class Product extends \Shop\Models\Prefabs\Product
{
    protected $default_options = array(
        // 'append' => true // set this to true so that ->bind() adds fields to $this->document even if they aren't in the default document structure
    );
    
    /**
     * Default document structure
     * @var array
     */
    protected $custom_fields = array(
        // Start Amrita Singh Custom Fields --------------------------------------
        'netsuite'=>array(
            'id'=>null,
            'recordtype'=>null,
            'parent'=>null,
            'last_modified'=>null,
            'class'=>null,
            'department'=>null,
            'currency'=>null                        
        ),
        'amrita'=>array(
            'type'=>null,
            'colors'=>null,
            'color_dual_tone'=>null,
            'measurements'=>null,
            'size'=>null,
            'earring_closure'=>null,
            'necklace_closure'=>null,
            'type'=>null,
            'material'=>null,
            'plating'=>null,
            'image_url'=>null,
            'thumb_image'=>null
        ),
        'prices'=>array(
        	'wholesale'=>null
        )
    );    
        
    public function __construct($source=array(), $options=array())
    {
        //$this->document['details'] = $this->document['details'] + $this->custom_fields;
        
        $this->mergeIntoDocument( $this->custom_fields );
                
        $this->setOptions($options);
    
        parent::__construct($source, $this->options);
    }
}