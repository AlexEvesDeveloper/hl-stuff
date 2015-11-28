<?php
/**
* Model definition for the cms news table
* 
*/
class Datasource_Cms_Gallery extends Zend_Db_Table_Multidb {
    protected $_name = 'gallery';
    protected $_primary = 'id';
    protected $_multidb = 'db_homelet_cms';
    
    public function addNew($categoryID, $filename, $width, $height) {
        $data = array(
            'category_id'   =>  $categoryID,
            'filename'      =>  $filename,
            'width'         =>  $width,
            'height'        =>  $height,
            'datetime'      =>  new Zend_DB_Expr('NOW()')
        );
        
        return $this->insert($data);
    }
    
    
    /**
     * Return all news articles sorted by date/time
     *
     * @return array
     *
     */
    public function getImagesByCategoryID($categoryID) {
        $select = $this->select();
        $select->where('category_id = ?',$categoryID);
        $select->order('filename DESC');
        
        $rows = $this->fetchAll($select);
        $imagesArray = $rows->toArray();
        
        $returnArray = array();
        foreach ($imagesArray as $image) {
            array_push($returnArray, array(
                'id'        =>  $image['id'],
                'fileName'  =>  $image['filename'],
                'width'     =>  $image['width'],
                'height'    =>  $image['height'],
                'niceDate'  =>  date('d-m-Y',strtotime($image['datetime'])),
            ));
        }
        
        return $returnArray;
    }
    
    
    public function getFilenameByID($imageID) {
        $select = $this->select();
        $select->where('id = ?', $imageID);
        
        $row = $this->fetchRow($select)->toArray();
        
        return $row['filename'];
    }
}
?>