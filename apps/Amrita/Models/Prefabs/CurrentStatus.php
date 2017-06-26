<?php
namespace Amrita\Models\Prefabs;

class CurrentStatus extends \Dsc\Prefabs
{
    protected $default_options = array(
        'append' => true
    );
    
    protected $document = array(
        'search_id' => null,        // int
        'totalResults' => null,     // int
        'resultsOffset' => null,    // int
        'resultsLimit' => null,     // int  
        'is_finished' => null,      // boolean
        'mode' => 1, 			// int 1 => sync products and categories, 2 => synch related products, 3 => end
        'finished' => array()       // Metastamp
        // will also include fields from a Pagination object
    );
}