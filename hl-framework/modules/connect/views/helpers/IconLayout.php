<?php

class Connect_View_Helper_IconLayout extends Zend_View_Helper_Abstract
{

    public function iconLayout($icons, $perRow=3) {
        $alt = '_alt';
        $colour = 'orange';
        $i=0;

        ?>
        <ul class="connect_button_list">
            <?php
            foreach($icons as $icon) {
                if (isset($icon['style'])){ $style = "_{$icon['style']}"; }else{ $style ="";}
                if (!isset($icon['size'])) $icon['size'] = 'single';

                $i++;
                if ('double' == $icon['size']) $i++;
                // If we have an even number of icons per row - we have to do some funkiness with the alternating
                if ($perRow %2 == 0) {
                    if (($i-1)%$perRow != 0) {
                        if ($alt=='_alt') { $alt=''; } else { $alt='_alt'; }
                        if ($colour=='blue') { $colour='orange'; } else { $colour='blue'; }
                    }
                } else {
                     if ($alt=='_alt') { $alt=''; } else { $alt='_alt'; }
                     if ($colour=='blue') { $colour='orange'; } else { $colour='blue'; }
                }
                if (strtolower(substr($icon['url'], 0, 4)) == 'http' || strtolower(substr($icon['url'], -4, 4)) == '.pdf') {
                    // If the link is in fact an offsite link as indicated by an absolute URL,
                    //   or is a PDF, open it in a new window
                    $target = '_blank';
                } else { $target = ''; }

                $pdfIcon = false;
                if (strtolower(substr($icon['url'], -4, 4)) == '.pdf') {
                    $params = Zend_Registry::get('params');
                    $basePath = $params->connect->basePublicPath;
                    $thumbnailUrl = substr($icon['url'], 0, -4) . '.thumb.jpg';

                    if (file_exists($basePath . $thumbnailUrl)) {
                        $pdfIcon = true;
                    }
                }

                if ($pdfIcon && !isset($icon['special'])) {

                    $pdfVersion = (isset($icon['pdf_info']['version']) && $icon['pdf_info']['version'] != '') ? $icon['pdf_info']['version'] : 'Current version';
                    $pdfReleaseDate = (isset($icon['pdf_info']['release_date']) && $icon['pdf_info']['release_date'] != '') ? $icon['pdf_info']['release_date'] : 'Unknown release date';
                    $pdfDescription = (isset($icon['pdf_info']['description']) && $icon['pdf_info']['description'] != '') ? $icon['pdf_info']['description'] : "{$icon['title']}.";

                    ?>
                    <li class="pdf_icon">
                        <div class="connect_pdf <?php if ($i%$perRow == 0) echo 'line_end'; ?>">
                            <div class="pdf_info">
                                <a href="<?php echo $icon['url']; ?>" target="<?php echo $target; ?>"><img src="<?php echo $thumbnailUrl; ?>" alt="<?php echo ucwords($icon['title']); ?>" /></a>
                                <h4><?php echo $icon['title']; ?></h4>
                                   <a class="connect_pdf_view" href="<?php echo $icon['url']; ?>" target="<?php echo $target; ?>">View</a>
                                <a class="connect_pdf_email emailPdfToAny" href="<?php echo $icon['url']; ?>" target="<?php echo $target; ?>">Email</a>

                                <!-- <p class="pdf_version"><?php echo $pdfVersion; ?> (<span class="pdf_release_date"><?php echo $pdfReleaseDate; ?></span>)</p> -->
                                <p class="pdf_description"><?php echo $pdfDescription; ?></p>
                            </div>
                        </div>
                    </li>
                    <?php
                } elseif (isset($icon['special'])) {
                    $icon['alt'] = $alt;
                    $icon['lineEnd'] = ($i % $perRow == 0);
                    echo $this->view->partial('partials/icon-special-' . $icon['special'] . '.phtml', $icon);
                  } else {
                    ?>
                    <li class="<?php if ($i%$perRow == 0) echo 'line_end'; ?> <?php echo $icon['size']; ?>_button">
                        <a href="<?php echo $icon['url'];?>"  target="<?php echo $target; ?>" <?php if (isset($icon['javascript'])) { ?>onClick="<?php echo $icon['javascript']; ?>"<?php } ?>>
                            <div class="connect_button<?php echo $alt.$style; ?>">
                                <img src="/assets/connect/images/icons/white-<?php echo $icon['icon']; ?>.png" alt="<?php echo $icon['title']; ?>" width="104" height="100" />
                                <h5><?php echo $icon['title']; ?></h5>
                                <?php
                                if (isset($icon['help_text']) && $icon['help_text'] != '') {
                                    ?>
                                    <span class="help_text"><?php echo $icon['help_text']; ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                        </a>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <?php
        return true;
    }
}

/* UNCOMMENT THIS CODE TO DO THE INSANE CENTER ALIGNING NONSENSE

<?php

class Connect_View_Helper_IconLayout extends Zend_View_Helper_Abstract
{

    public function iconLayout($icons, $perRow=3) {
        $alt = '_alt';
        $colour = 'orange';
        $i=0;
        ?>
        <div class="connect_button_list">
            <?php
            foreach($icons as $icon) {
                $i++;
                // If we have an even number of icons per row - we have to do some funkiness with the alternating
                if ($perRow %2 == 0) {
                    if (($i-1)%$perRow != 0) {
                        if ($alt=='_alt') { $alt=''; } else { $alt='_alt'; }
                        if ($colour=='blue') { $colour='orange'; } else { $colour='blue'; }
                    }
                } else {
                     if ($alt=='_alt') { $alt=''; } else { $alt='_alt'; }
                     if ($colour=='blue') { $colour='orange'; } else { $colour='blue'; }
                }
                if (strtolower(substr($icon['url'], 0, 4)) == 'http' || strtolower(substr($icon['url'], -4, 4)) == '.pdf') {
                    // If the link is in fact an offsite link as indicated by an absolute URL,
                    //   or is a PDF, open it in a new window
                    $target = '_blank';
                } else { $target = ''; }

                $pdfIcon = false;
                if (strtolower(substr($icon['url'], -4, 4)) == '.pdf') {
                    $params = Zend_Registry::get('params');
                    $basePath = $params->connect->basePublicPath;
                    $thumbnailUrl = substr($icon['url'], 0, -4) . '.thumb.jpg';

                    if (file_exists($basePath . $thumbnailUrl)) {
                        $pdfIcon = true;
                    }
                }

                if ($pdfIcon && !isset($icon['special'])) {

                    $pdfVersion = (isset($icon['pdf_info']['version'])) ? $icon['pdf_info']['version'] : 'Current version';
                    $pdfReleaseDate = (isset($icon['pdf_info']['release_date'])) ? $icon['pdf_info']['release_date'] : 'Unknown release date';
                    $pdfDescription = (isset($icon['pdf_info']['description'])) ? $icon['pdf_info']['description'] : "{$icon['title']}.";

                    ?>
                    <span class="connect_pdf <?php if ($i%$perRow == 0) echo 'line_end'; ?>">
                        <a href="<?php echo $icon['url']; ?>" target="<?php echo $target; ?>">
                            <img src="<?php echo $thumbnailUrl; ?>" alt="<?php echo ucwords($icon['title']); ?>" />
                        </a>
                        <div class="pdf_info">
                            <h4><?php echo $icon['title']; ?></h4>
                            <p class="pdf_version"><?php echo $pdfVersion; ?> (<span class="pdf_release_date"><?php echo $pdfReleaseDate; ?></span>)</p>
                            <p class="pdf_description"><?php echo $pdfDescription; ?></p>
                            <a class="connect_pdf_view" href="<?php echo $icon['url']; ?>" target="<?php echo $target; ?>">View</a>
                            <a class="connect_pdf_email" href="<?php echo $icon['url']; ?>" target="<?php echo $target; ?>" class="emailPdfToAny">Email</a>
                        </div>
                    </span>
                    <?php
                } elseif (isset($icon['special'])) {
                    $icon['alt'] = $alt;
                    echo $this->view->partial('partials/icon-special-' . $icon['special'] . '.phtml', $icon);
                } else {
                    ?>
                    <span class="connect_button<?php echo $alt; ?>">
                        <a href="<?php echo $icon['url'];?>"  target="<?php echo $target; ?>" <?php if (isset($icon['javascript'])) { ?>onClick="<?php echo $icon['javascript']; ?>"<?php } ?>>
                            <img src="/assets/connect/images/icons/white_<?php echo $icon['icon']; ?>.png" alt="<?php echo $icon['title']; ?>" width="104" height="100" />
                            <span><?php echo $icon['title']; ?></span>
                        </a>
                    </span>
                    <?php
                }
            }
            ?>
        </ul>
        <?php
        return true;
    }
}

*/