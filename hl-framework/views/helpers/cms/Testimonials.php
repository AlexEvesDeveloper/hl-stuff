<?php
class Cms_View_Helper_Testimonials extends Zend_View_Helper_Abstract
{
    public function testimonials($tags = '')
    {
        if (!$tags) { $tags = ''; }
        $tags = trim($tags,' ');
        $tags = trim($tags,','); // Trim any excess commas off the list
        $tagsArray = explode(',',$tags); // Convert the list to an array
        
        // Now we have an array of testimonial tags - we need to filter for them in the select
        $testimonials = new Datasource_Cms_Testimonials();
        $testimonialsArray = $testimonials->getByTags($tagsArray);
        
        return $this->view->partialLoop('templates/partials/testimonial.phtml', $testimonialsArray);
    }
}
?>