<?php

/**
 * View helper to render sortable table column headers
 * 
 * @package Account_View_Helper_SortColumn
 */
class Account_View_Helper_SortableColumn extends Zend_View_Helper_Abstract
{
    /**
     * Returns the sortable column header for tables
     * 
     * <code>
     * <!-- Inside view -->
     * <table>
     *     <thead>
     *         <tr>
     *             <th><?php echo $this->sortableColumn('Name', 'column') ?></th>
     *             ...
     * 
     * </code>
     * 
     * @param string $name
     * @param string $column
     * @param string $defaultOrder The default sort order [asc|desc]
     * @return string 
     */
    public function sortableColumn($name, $column, $defaultOrder = 'asc')
    {
        $arrow = '';
        $title = '';

        $queryString = urldecode($_SERVER['QUERY_STRING']);
        
        $queryString = preg_replace('/order\[(.*)\]=(asc|desc)/i', '', $queryString);
        
        if ($queryString && strlen($queryString)) {
            if (strpos($queryString, '&') === false) {
                $queryString = '&' . $queryString;
            }
            else {
                $queryStringLen = strlen($queryString) - 1;
                if ('&' == $queryString{$queryStringLen}) {
                    $queryString = '&' . substr($queryString, 0, $queryStringLen);
                }    
            }
        }
        
        $url = sprintf('?order[%s]=%s%s', $column, $defaultOrder, $queryString);
        $order = $this->_getRequest()->getParam('order', array());
        
        if (is_array($order) && count($order)) {
            $orderColumn = key($order);
            $orderSort = $order[$orderColumn];
            if ($orderColumn == $column) {
                if ('asc' == $orderSort) {
                    $arrow = '<span class="sortable-column-arrow">&darr;</span>';
                    $title = sprintf('Sort %s Descending', $name);
                    $url = sprintf('?order[%s]=desc%s', $orderColumn, $queryString);
                } elseif('desc' == $orderSort) {
                    $arrow = '<span class="sortable-column-arrow">&uarr;</span>';
                    $title = sprintf('Sort %s Ascending', $name);
                    $url = sprintf('?order[%s]=asc%s', $orderColumn, $queryString);
                }
            }
        }
        
        return sprintf('<span class="sortable-column"><a href="%s" title="%s">%s %s</a></span>', 
                $url, 
                $title, 
                $name,
                $arrow);
    }
    
    /**
     * Get the current request from the controller
     * 
     * @return Zend_Controller_Request_Abstract
     */
    private function _getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }
}