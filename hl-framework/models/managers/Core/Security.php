<?php

/**
* Security/MAC - PHP4 compatible helper class
* Generation and verification of (Hash-based) Message Authentication Code
* See http://en.wikipedia.org/wiki/Message_authentication_code and http://en.wikipedia.org/wiki/HMAC
* Generation: For serializing short string-based data and appending an optional timestamp, and a secure verification hash, as a MAC
* Authentication: For checking an incoming symmetric MAC
*
* Changelog:
* 2010-07-28 initial version, Paul Swift
* 2011-05-22 re-written in PHP 5, Ben Vickers
*/

// Todo: Shouldn't this be an Application_Core_Security library class rather than a manager? - PB

class Manager_Core_Security {
    
    protected $_secretString;
    protected $_timestampInclude;
    protected $_timestampVariance;
    protected $_separator = '||';
    protected $_separatorRegexFriendly = '\|\|';
    protected $_hashAlgorithm = 'sha1';
    protected $_hashTruncateLength = 8;

    //Crypto block size for HMAC algorithm, not currently in use
    protected $_hashAlgorithmBlocksize;


    public function __construct($secretString = '', $timestampInclude = true, $timestampVariance = 86400) {
        
        $this->setSecretString($secretString);
        $this->setTimestampInclude($timestampInclude);
        $this->setTimestampVariance($timestampVariance);
        
        $this->_hashAlgorithmBlocksize = array(
        'md5' => 128,
        'sha1' => 160);
    }

    function setSecretString($secretString)
    {
        if ((is_string($secretString)) && ($secretString != '')) {
            $this->_secretString = $secretString;
            return true;
        }
        return false;
    }

    function setTimestampInclude($timestampInclude)
    {
        if (is_bool($timestampInclude)) {
            $this->_timestampInclude = $timestampInclude;
            return true;
        }
        return false;
    }

    function setTimestampVariance($timestampVariance)
    {
        if (is_numeric($timestampVariance)) {
            $this->_timestampVariance = $timestampVariance;
            return true;
        }
        return false;
    }

    // Not currently in use - can be utilised for MAC algorithms that are non-HMAC
    function localHash($algo, $data)
    {
        // Stand-in function for PHP5's hash()
        // Could have been simply 'return $algo($data);' but enforces only $algo == 'md5' or $algo == 'sha1'
        switch($algo) {
            case 'md5':
                $hash = md5($data);
                break;

            case 'sha1':
            default:
                $hash = sha1($data);
                break;
        }

        return $hash;
    }

    function localHashHmac($algo, $data, $key, $raw_output = false)
    {
        // Stand-in function for PHP5's hash_hmac()
        // Adapted from KC Cloyd's custom_hmac() at http://www.php.net/manual/en/function.hash-hmac.php (imitates PHP5's hash_hmac())

        $algo = strtolower($algo);
        $pack = 'H' . strlen($algo('test'));
        $size = 64; // Should probably be based on $this->_hashAlgorithmBlocksize according to wiki article, but we want a workalike of hash_hmac()
        $opad = str_repeat(chr(0x5C), $size);
        $ipad = str_repeat(chr(0x36), $size);

        if (strlen($key) > $size) {
            $key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
        } else {
            $key = str_pad($key, $size, chr(0x00));
        }

        for ($i = 0; $i < strlen($key) - 1; $i++) {
            $opad[$i] = $opad[$i] ^ $key[$i];
            $ipad[$i] = $ipad[$i] ^ $key[$i];
        }

        $output = $algo($opad . pack($pack, $algo($ipad . $data)));

        return ($raw_output) ? pack($pack, $output) : $output;
    }

    // Expects an ordered key => val paired array of data (*only* uses the vals)
    //   eg, array('userId' => '12', 'userName' => 'fred', 'userType' => 'web');
    // Returns a MAC token
    function generate($data)
    {
        $output = '';

        // Add timestamp
        if ($this->_timestampInclude) {
            $output .= time() . $this->_separator;
        }

        // Add data
        foreach($data as $key => $val) {
            $output .= "{$val}{$this->_separator}";
        }
        // Strip off last separator
        $output = substr($output, 0, -strlen($this->_separator));

        // Generate hash with data and secret string - in PHP5 use hash_hmac()
        //$hash = $this->localHashHmac($this->_hashAlgorithm, $output, $this->_secretString);
        $hash = hash_hmac($this->_hashAlgorithm, $output, $this->_secretString);
        
        // Trim hash
        if ($this->_hashTruncateLength > 0) {
            $hash = substr($hash, 0, $this->_hashTruncateLength);
        }
        // Add hash
        $output .= $this->_separator . $hash;

        // Return base64 encoded MAC token
        $mac = base64_encode($output);
        return $mac;
    }

    // Expects a base64 encoded MAC token and an ordered key => val paired array of data (*only* uses the vals)
    //   eg, array(0 => 'userId', 1 => 'userName', 2 => 'userType');
    // On success returns an array containing a result boolean set to true and a data array using the input data keys
    //   eg, array('result' => true, 'data' => array('userId' => '12', 'userName' => 'fred', 'userType' => 'web'));
    // On failure returns as array containing a result boolean set to false and an error description
    //    eg, array('result' => false, 'error' => 'timestamp out of bounds');
    function authenticate($mac, $dataKeys)
    {
        // First base64 decode string and extract hash
        $mac = base64_decode($mac);
        preg_match('/^(.*)' . $this->_separatorRegexFriendly . '(.*)$/', $mac, $matches);
        // All the incoming data in the MAC (inc. optional timestamp at start), no hash
        $inputDataString = $matches[1];
        // The incoming data as an array
        $inputData = explode($this->_separator, $inputDataString);
        // The incoming hash in the MAC
        $inputHash = $matches[2];

        // Generate verification hash with data and secret string - in PHP5 use hash_hmac()
        //$verificationHash = $this->localHashHmac($this->_hashAlgorithm, $inputDataString, $this->_secretString);
        $verificationHash = hash_hmac($this->_hashAlgorithm, $inputDataString, $this->_secretString);
        
        // Trim verification hash
        if ($this->_hashTruncateLength > 0) {
            $verificationHash = substr($verificationHash, 0, $this->_hashTruncateLength);
        }

        // Check hashes match (data/hash untampered)
        if ($inputHash == $verificationHash) {
            // Check if timestamp is in use
            if ($this->_timestampInclude) {
                $inputTimestamp = $inputData[0];
                // Check timestamp in bounds
                $now = time();
                if (($inputTimestamp > $now - $this->_timestampVariance) && ($inputTimestamp < $now + $this->_timestampVariance)) {
                    // Success - move data into output array (without timestamp) and return it
                    $outputData = array();
                    foreach ($dataKeys as $key => $val) {
                        $outputData[$val] = $inputData[$key + 1]; // +1 offset as the incoming timestamp occupies $inputData[0]
                    }
                    return array('result' => true, 'data' => $outputData);
                } else {
                    return array('result' => false, 'error' => 'timestamp out of bounds');
                }
            }
            // Success - move data into output array and return it
            $outputData = array();
            foreach ($dataKeys as $key => $val) {
                $outputData[$val] = $inputData[$key];
            }
            return array('result' => true, 'data' => $outputData);
        } else {
            return array('result' => false, 'error' => 'hash didn\'t verify');
        }

    }

}