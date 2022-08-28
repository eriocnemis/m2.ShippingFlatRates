<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\ShippingFlatRates\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Eriocnemis\ShippingFlatRates\Model\GetFreeBoxesCount;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Model\Order\Item;
use Magento\Catalog\Model\Product;

/**
 * Test free boxes calculation
 */
class GetFreeBoxesCountTest extends TestCase
{
    /**
     * @var GetFreeBoxesCount
     */
    private $getFreeBoxesCount;

    /**
     * Prepare test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        /** @var GetFreeBoxesCount $getFreeBoxesCount */
        $getFreeBoxesCount = $objectManager->getObject(
            GetFreeBoxesCount::class,
            []
        );

        $this->getFreeBoxesCount = $getFreeBoxesCount;
    }

    /**
     * Test free boxes calculation
     *
     * @param int $isVirtual
     * @param int $freeShipping
     * @param int $qty
     * @param int $result
     * @return void
     * @dataProvider dataProviderFreeBoxes
     * @test
     */
    public function execute($isVirtual, $freeShipping, $qty, $result): void
    {
        $request = $this->getMockBuilder(RateRequest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllItems'])
            ->getMock();

        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['isVirtual'])
            ->getMock();

        $item = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getProduct',
                    'getParentItem',
                    'getHasChildren',
                    'isShipSeparately',
                    'getChildren',
                    'getQty',
                    'getFreeShipping'
                ]
            )
            ->getMock();

        $product->expects($this->any())->method('isVirtual')->willReturn($isVirtual);

        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $item->expects($this->any())->method('getFreeShipping')->willReturn($freeShipping);
        $item->expects($this->any())->method('getQty')->willReturn($qty);

        $request->expects($this->any())->method('getAllItems')->willReturn([$item]);

        $this->assertEquals($result, $this->getFreeBoxesCount->execute($request));
    }

    /**
     * Data provider of free boxes count test
     *
     * @return mixed[]
     */
    public function dataProviderFreeBoxes()
    {
        return [
            [1, 1, 1, 0],
            [1, 0, 1, 0],
            [0, 0, 2, 0],
            [0, 1, 2, 2],
            [0, 1, 1, 1]
        ];
    }
}
