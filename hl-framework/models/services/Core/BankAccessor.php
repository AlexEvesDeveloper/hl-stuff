<?php
/**
 * Class for remotely validating bank account numbers
 *
 */
class Service_Core_BankAccessor {

    /**
     * Validates a bank sort code and account number combination
     *
     * Returns True if valid, false otherwise.
     *
     * @param string $sort_code
     * @param string $acct_number
     * @return bool success
     */
    public function isValid($sort_code, $acct_number) {
        $core_bank = new Manager_Core_Bank();
        return $core_bank->isAccountNumberValid($sort_code, $acct_number);
    }
}