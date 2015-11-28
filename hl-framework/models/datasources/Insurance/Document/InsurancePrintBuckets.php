<?php

final class Datasource_Insurance_Document_InsurancePrintBuckets extends Zend_Db_Table_Multidb
{
    protected $_multidb = 'db_legacy_homelet';
    protected $_name = 'printBucketList';
    protected $_primary = 'bucket_id';
    
    /**
     * Retrieve a bucket Id number
     *
     * @param string $bucketname Bucket name
     * @return int Bucket Id number
     */
    public function getBucketId($bucketname)
    {
        $select = $this->select()->where('bucket_name = ?', $bucketname);
        $row = $this->fetchRow($select);
      
        if (isset($row['bucket_id']))
            return $row['bucket_id'];
            
        return null;
    }
    
    /**
     * Retrieve the bucket name
     */
    public function getBucketName($bucketid)
    {
        $select = $this->select()->where('bucket_id = ?', $bucketid);
        $row = $this->fetchRow($select);
        
        if (isset($row['bucket_name']))
            return $row['bucket_name'];
        
        return null;
    }
}
