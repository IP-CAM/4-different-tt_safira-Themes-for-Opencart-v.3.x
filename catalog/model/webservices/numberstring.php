<?php 
class ModelWebservicesNumberstring extends Model {
    
    public function N2L($number)
    {
        $result = array();
        $tens = floor($number / 10);
        $units = $number % 10;

        $words = array
        (
            'units' => array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eightteen', 'nineteen'),
            'tens' => array('', '', 'twenty', 'thirty', 'fourty', 'fifty', 'sixty', 'seventy', 'eigthy', 'ninety')
        );

        if ($tens < 2)
        {
            $result[] = $words['units'][$tens * 10 + $units];
        }

        else
        {
            $result[] = $words['tens'][$tens];

            if ($units > 0)
            {
                $result[count($result) - 1] .= '-' . $words['units'][$units];
            }
        }

        if (empty($result[0]))
        {
            $result[0] = 'zero';
        }

        return trim(implode(' ', $result));
    }
    
}