<?php
namespace Shop\Constants;

class OrderFinancialStatus 
{
    const pending = "pending";
    const authorized = "authorized";
    const partially_paid = "partially_paid";
    const paid = "paid";
    const partially_refunded = "partially_refunded";
    const refunded = "refunded";
    const voided = "voided";
    
    public static function fetch()
    {
        $refl = new \ReflectionClass( get_called_class() );
        return (array) $refl->getConstants();
    }
}