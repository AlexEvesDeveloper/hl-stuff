<?php

namespace RRP\Tests\Form\DataTransformer;
use Barbondev\IRISSDK\IndividualApplication\Product\Model\Product;
use Barbondev\IRISSDK\IndividualApplication\ReferencingApplication\Model\ReferencingApplication;
use Iris\Common\Enumerations\ProductIds;
use RRP\Form\DataTransformer\ReferenceToProductTypeTransformer;

/**
 * Class ReferenceToProductTypeTransformerTest
 *
 * @package RRP\Tests\Form\DataTransformer
 * @author Alex Eves <alex.eves@barbon.com>
 */
class ReferenceToProductTypeTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock_SessionReferenceHolder
     */
    protected $mockSessionHolder;

    /**
     * Pre test set up
     */
    public function setUp()
    {
        $this->mockSessionHolder = $this->getMockBuilder('RRP\Utility\SessionReferenceHolder')
            ->disableOriginalConstructor()
            ->setMethods(array('getReferencesFromSession'))
            ->getMock();
    }

    /**
     * @test
     */
    public function does_not_transform_value_if_value_is_not_homelet_reference()
    {
        $transformer = new ReferenceToProductTypeTransformer($this->mockSessionHolder);

        $this->assertEquals('OtherCC', $transformer->reverseTransform('OtherCC'));
    }

    /**
     * @test
     */
    public function does_not_transform_if_reference_numbers_are_yet_to_be_added_to_the_form()
    {
        $this->mockSessionHolder
            ->method('getReferencesFromSession')
            ->willReturn(false);

        $transformer = new ReferenceToProductTypeTransformer($this->mockSessionHolder);

        $this->assertNull($transformer->reverseTransform('HomeLetReference'));
    }

    /**
     * @test
     * @dataProvider getValidInsightCombinations
     */
    public function transforms_a_valid_insight_into_insight(array $references)
    {
        $this->mockSessionHolder
            ->method('getReferencesFromSession')
            ->willReturn($references);

        $transformer = new ReferenceToProductTypeTransformer($this->mockSessionHolder);

        $this->assertEquals('Insight', $transformer->reverseTransform('HomeLetReference'));
    }

    /**
     * @test
     * @dataProvider getInvalidInsightValidEnhanceCombinations
     */
    public function transforms_an_invalid_insight_and_valid_enhance_into_enhance(array $references)
    {
        $this->mockSessionHolder
            ->method('getReferencesFromSession')
            ->willReturn($references);

        $transformer = new ReferenceToProductTypeTransformer($this->mockSessionHolder);

        $this->assertEquals('Enhance', $transformer->reverseTransform('HomeLetReference'));
    }

    /**
     * @test
     * @dataProvider getInvalidInsightInvalidEnhanceValidOptimumCombinations
     */
    public function transforms_invalid_insight_and_invalid_enhance_and_valid_optimum_into_optimum(array $references)
    {
        $this->mockSessionHolder
            ->method('getReferencesFromSession')
            ->willReturn($references);

        $transformer = new ReferenceToProductTypeTransformer($this->mockSessionHolder);

        $this->assertEquals('Optimum', $transformer->reverseTransform('HomeLetReference'));
    }

    /**
     * Return combinations containing valid insight references.
     *
     * @return array
     */
    public function getValidInsightCombinations()
    {
        $insightProduct = new Product();
        $insightProduct->setName('Insight');
        $insight = new ReferencingApplication();
        $insight->setProduct($insightProduct);
        $insight->setProductId(ProductIds::INSIGHT);
        $insight->setRentShare(1);

        $enhanceProduct = new Product();
        $enhanceProduct->setName('Enhance');
        $enhance = new ReferencingApplication();
        $enhance->setProduct($enhanceProduct);
        $enhance->setProductId(ProductIds::ENHANCE);
        $enhance->setRentShare(1);

        $optimumProduct = new Product();
        $optimumProduct->setName('Optimum');
        $optimum = new ReferencingApplication();
        $optimum->setProduct($optimumProduct);
        $optimum->setProductId(ProductIds::OPTIMUM);
        $optimum->setRentShare(1);

        return array(
            array(array($insight)),
            array(array($insight, $enhance)),
            array(array($insight, $optimum)),
            array(array($insight, $enhance, $optimum)),
        );
    }

    /**
     * Return combinations containing invalid insight and valid enhance references.
     *
     * @return array
     */
    public function getInvalidInsightValidEnhanceCombinations()
    {
        $insightProduct = new Product();
        $insightProduct->setName('Insight');
        $insight = new ReferencingApplication();
        $insight->setProduct($insightProduct);
        $insight->setProductId(ProductIds::INSIGHT);
        $insight->setRentShare(0);

        $enhanceProduct = new Product();
        $enhanceProduct->setName('Enhance');
        $enhance = new ReferencingApplication();
        $enhance->setProduct($enhanceProduct);
        $enhance->setProductId(ProductIds::ENHANCE);
        $enhance->setRentShare(1);

        $optimumProduct = new Product();
        $optimumProduct->setName('Optimum');
        $optimum = new ReferencingApplication();
        $optimum->setProduct($optimumProduct);
        $optimum->setProductId(ProductIds::OPTIMUM);
        $optimum->setRentShare(1);

        return array(
            array(array($insight, $enhance)),
            array(array($insight, $enhance, $optimum)),
        );
    }

    /**
     * Return combinations containing invalid insight, invalid enhance and valid optimum references.
     *
     * @return array
     */
    public function getInvalidInsightInvalidEnhanceValidOptimumCombinations()
    {
        $insightProduct = new Product();
        $insightProduct->setName('Insight');
        $insight = new ReferencingApplication();
        $insight->setProduct($insightProduct);
        $insight->setProductId(ProductIds::INSIGHT);
        $insight->setRentShare(0);

        $enhanceProduct = new Product();
        $enhanceProduct->setName('Enhance');
        $enhance = new ReferencingApplication();
        $enhance->setProduct($enhanceProduct);
        $enhance->setProductId(ProductIds::ENHANCE);
        $enhance->setRentShare(0);

        $optimumProduct = new Product();
        $optimumProduct->setName('Optimum');
        $optimum = new ReferencingApplication();
        $optimum->setProduct($optimumProduct);
        $optimum->setProductId(ProductIds::OPTIMUM);
        $optimum->setRentShare(1);

        return array(
            array(array($insight, $optimum)),
            array(array($enhance, $optimum)),
            array(array($insight, $enhance, $optimum)),
        );
    }
}