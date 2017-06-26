<?php 
namespace Amrita\Prefabs;

class AttributeOption extends \Shop\Models\Prefabs\AttributeOption
{
    protected $custom_fields = array(
        // Start Amrita Singh Custom Fields --------------------------------------
        'netsuite'=>array(
            'id'=>null,
        )
    );    
        
    public function __construct($source=array(), $options=array())
    {
        $this->mergeIntoDocument( $this->custom_fields );
                
        $this->setOptions($options);
    
        parent::__construct($source, $this->options);
    }
}