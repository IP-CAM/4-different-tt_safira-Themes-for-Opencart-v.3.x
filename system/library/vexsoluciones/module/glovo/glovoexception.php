<?php
namespace Vexsoluciones\Module\Glovo;

use Vexsoluciones\Module\ShippingGlovoConstants;

class GlovoException extends \Exception
{
    public function __construct($message, $code = 500, \Exception $previous = null)
    {
        ShippingGlovoConstants::logger()->write('Error: '. $message);
        parent::__construct($message, $code, $previous);
    }
}