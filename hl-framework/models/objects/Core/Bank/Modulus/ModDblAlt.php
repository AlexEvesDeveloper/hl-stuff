<?php

class Model_Core_Bank_Modulus_ModDblAlt extends Model_Core_Bank_ModulusCalc
{
    /**
     * Override default total calculation for custom double alternate
     * calculation
     */
    public function calculateTotal()
    {
        $sortCodeAccountMerge = $this->getSortCodeAccountMerge();
        
        $validationcode_values = Array
        (
            $this->_multiplier->sortCodeU,
            $this->_multiplier->sortCodeV,
            $this->_multiplier->sortCodeW,
            $this->_multiplier->sortCodeX,
            $this->_multiplier->sortCodeY,
            $this->_multiplier->sortCodeZ,
            $this->_multiplier->accountNumberA,
            $this->_multiplier->accountNumberB,
            $this->_multiplier->accountNumberC,
            $this->_multiplier->accountNumberD,
            $this->_multiplier->accountNumberE,
            $this->_multiplier->accountNumberF,
            $this->_multiplier->accountNumberG,
            $this->_multiplier->accountNumberH,
        );
        
        $digits = Array
        (
            $sortCodeAccountMerge->sortCodeU,
            $sortCodeAccountMerge->sortCodeV,
            $sortCodeAccountMerge->sortCodeW,
            $sortCodeAccountMerge->sortCodeX,
            $sortCodeAccountMerge->sortCodeY,
            $sortCodeAccountMerge->sortCodeZ,
            $sortCodeAccountMerge->accountNumberA,
            $sortCodeAccountMerge->accountNumberB,
            $sortCodeAccountMerge->accountNumberC,
            $sortCodeAccountMerge->accountNumberD,
            $sortCodeAccountMerge->accountNumberE,
            $sortCodeAccountMerge->accountNumberF,
            $sortCodeAccountMerge->accountNumberG,
            $sortCodeAccountMerge->accountNumberH,
        );
        
        $total = 0;
        $product = '';
        $digitlist = array();
        $len = count($digits) - 1;
        for ($i = 0; $i <= $len; $i++)
        {
            $num = 0;
            
            if ($validationcode_values[$i])
            {
                if ($i % 2 == 0)
                {
                    $num = $validationcode_values[$i] * $digits[$i];
                }
                else
                {
                    $num = $digits[$i];
                }
            }
            
            $product .= $num;
        }
        
        // Split by char.
        // Note: This is done since the $num var above may contain a value greater than 9
        //       The algorithm must add EACH number individually in the sequence in order to work.
        $productlist = preg_split('//', $product, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($productlist as $productdigit)
        {
            $total += $productdigit;
        }
        
        $this->_total = $total;
    }
}
