<?php
/**
 * Class for remotely validating bank account numbers
 *
 */
class Service_Core_RGProductOfferedAccessor {

    /**
     * Fetches all products
     *
     * @param string $fsa_status
     * @param int $letType
     * @param int $howRgOffered
     * @return array products data
     */
    public function fetchProducts($fsa_status, $letType, $howRgOffered) {
        
        $rgp_offered = new Manager_Core_RGProductOffered();
        return $rgp_offered->fetchProducts($fsa_status, $letType, $howRgOffered);
    }
}