<?php 
namespace Amrita\Prefabs;

class Variant extends \Shop\Models\Prefabs\Variant
{
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
            'default'=>null,
            'list'=>null,
            'wholesale'=>null                        
        )
    );    
        
    public function __construct($source=array(), $options=array())
    {
        $this->mergeIntoDocument( $this->custom_fields );
                
        $this->setOptions($options);
    
        parent::__construct($source, $this->options);
    }
}