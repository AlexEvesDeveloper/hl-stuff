<?php

class Cms_View_Helper_EmbeddedVideo extends Zend_View_Helper_Abstract
{

    private $_outputTypes = array('video', 'thumbnail', 'thumbnailUrl');

    public function embeddedVideo($url, $width, $additionalOptions = array())
    {

        // Additional options
        $options = array(
            'output' => 'video',
            'showControls' => false
        );

        if (isset($additionalOptions['output']) && in_array($additionalOptions['output'], $this->_outputTypes)) {
            $options['output'] = $additionalOptions['output'];
        }

        if (isset($additionalOptions['showControls']) && $additionalOptions['showControls']) {
            $options['showControls'] = true;
        }

        if (stripos($url, 'youtu.be') !== false) {

            // YouTube video (shortened URL style, eg, http://youtu.be/Gu-yRVDflbI)

            // Extract video ID from short URL
            $videoId = preg_replace('/^.*youtu\.be.*\/([\w\-]+).*$/i', '$1', $url);

            // Calculate aspect ratio
            //$height = floor($width * 0.9); // YouTube standard ratio is 0.9
            $height = floor($width * 0.75); // Now showing controller-less

            // Return YouTube snippet
            return $this->view->partial(
                'templates/partials/video-youtube.phtml',
                array(
                    'videoId' => $videoId,
                    'width' => $width,
                    'height' => $height,
                    'options' => $options
                )
            );

        } elseif (stripos($url, 'youtube.com') !== false) {

            // YouTube video

            // Extract video ID from potentially dirty URL or a chunk of HTML like the "embed" source
            $videoId = preg_replace('/^.*youtube\.com.*\/(.*\?v=)?([\w\-]+).*$/i', '$2', $url);

            // Calculate aspect ratio
            //$height = floor($width * 0.9); // YouTube standard ratio is 0.9
            $height = floor($width * 0.75); // Now showing controller-less

            // Return YouTube snippet
            return $this->view->partial(
                'templates/partials/video-youtube.phtml',
                array(
                    'videoId' => $videoId,
                    'width' => $width,
                    'height' => $height,
                    'options' => $options
                )
            );

        } elseif (stripos($url, 'vimeo.com') !== false) {

            // Vimeo video

            // Extract video ID from potentially dirty URL or a chunk of HTML like the "embed" source
            $videoId = preg_replace('/^.*vimeo\.com.*\/([\w\-]+).*$/i', '$1', $url);

            // Calculate aspect ratio
            $height = floor($width * 0.75); // Vimeo standard ratio is 0.75

            // Return Vimeo snippet
            return $this->view->partial(
                'templates/partials/video-vimeo.phtml',
                array(
                    'videoId' => $videoId,
                    'width' => $width,
                    'height' => $height,
                    'options' => $options
                )
            );

        }
    }
}
?>