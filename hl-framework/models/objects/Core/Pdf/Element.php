<?php

/**
 * Represents a visual element, typically a chunk of text, for merging into a
 * PDF using Application_Core_PdfMerge.
 *
 * @category   Model
 * @package    Model_Core
 * @subpackage Pdf
 */
class Model_Core_Pdf_Element extends Model_Abstract {

    /**
     * The page in which the element belongs.
     *
     * @var integer 0-indexed page number.
     */
    public $pageNumber;

    /**
     * The text to inject.
     *
     * @var string
     */
    public $text;

    /**
     * The x-coord - measured in points from the left.
     *
     * @var float
     */
    public $x;

    /**
     * The y-coord - measured in points from the bottom.  Note: can be flipped
     * by the merge() method of Application_Core_PdfMerge if an origin at the
     * top-left is desirable.
     *
     * @var float
     */
    public $y;

    /**
     * X-spacing - if set, is used in place of natural glyph spacing between
     * characters horizontally.
     *
     * @var float
     */
    public $xSpacing = null;

    /**
     * Y-spacing - if set, is used in place of natural glyph spacing between
     * characters vertically.
     *
     * @var float
     */
    public $ySpacing = null;

    /**
     * Flag to switch text wrapping on.  If on, $this->maxWidth becomes
     * mandatory.
     *
     * @var bool True = wrap text, $this->maxWidth becomes mandatory.
     */
    public $wrap = false;

    /**
     * Maximum width, in points, that a wrapped piece of text should be.  Only
     * used when $this->wrap is true.
     *
     * @var float
     */
    public $maxWidth = null;

    /**
     * Constructor to simplify quick instantiation of domain object.
     *
     * @param integer $pageNumber 0-indexed page number.
     * @param string $text The text to inject.
     * @param float $x The x-coord, measured in points.
     * @param float $y The y-coord, measured in points.
     * @param float $xSpacing Used in place of natural glyph horizontal spacing.
     * @param float $ySpacing Used in place of natural glyph vertical spacing.
     * @param bool $wrap Flag to switch text wrapping on.
     * @param float $maxWidth Maximum width, in points, that a wrapped piece of
     * text should be.
     *
     * @return void
     */
    public function __construct($pageNumber = null, $text = null, $x = null, $y = null, $xSpacing = null, $ySpacing = null, $wrap = false, $maxWidth = null) {

        $this->pageNumber = $pageNumber;
        $this->text =       $text;
        $this->x =          $x;
        $this->y =          $y;
        $this->xSpacing =   $xSpacing;
        $this->ySpacing =   $ySpacing;
        $this->wrap =       $wrap;
        $this->maxWidth =   $maxWidth;
    }
}