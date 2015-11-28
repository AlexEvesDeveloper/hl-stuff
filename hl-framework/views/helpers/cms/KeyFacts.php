<?php
class Cms_View_Helper_KeyFacts extends Zend_View_Helper_Abstract
{
    public function keyFacts($tags = 'global')
    {
        /* This is a copy of the testimonials as the facts are so similar it hurts! :(
           Need to refactor this to point at a key facts database in the CMS */
        
        /*if (!$tags) { $tags = 'global'; }
        $tags = trim($tags,' ');
        $tags = trim($tags,','); // Trim any excess commas off the list
        $tagsArray = explode(',',$tags); // Convert the list to an array
        
        // Now we have an array of testimonial tags - we need to filter for them in the select
        $testimonials = new Model_Cms_Testimonials();
        $testimonialsArray = $testimonials->getByTags($tagsArray);
        
        return $this->view->partialLoop('cms/templates/partials/testimonial.phtml', $testimonialsArray);*/
    }
}
?>