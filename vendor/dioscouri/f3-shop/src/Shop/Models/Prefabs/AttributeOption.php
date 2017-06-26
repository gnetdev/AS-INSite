<?php 
namespace Shop\Models\Prefabs;

class AttributeOption extends \Dsc\Prefabs
{
    /**
     * Default document structure
     * @var array
     */
    protected $document = array(
        'id'=>null,                     // (string) MongoId, unique id for this attribute option
        'value'=>null,                  // e.g. "Short", "Long", "Three-Quarter"
        'price_impact'=>null,           // inc, dec, new, null == this option will either increase, decrease, overwrite, or have no impact on the price
        'price_impact_amount'=>null,    // the absolute value of the change, e.g. 1.99, even if it is a negative price_impact
        'weight_impact'=>null,          // same as price_impact
        'weight_impact_amount'=>null,   // absolute value of the weight impact
        'ordering'=>0
                    
    );
        
    public function __construct($source=array(), $options=array())
    {
        $this->set('id', (string) new \MongoId );
        parent::__construct( $source, $options );
    }
}