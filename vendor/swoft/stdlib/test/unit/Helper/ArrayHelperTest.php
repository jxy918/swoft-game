<?php declare(strict_types=1);

namespace SwoftTest\Stdlib\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Swoft\Stdlib\Helper\ArrayHelper;

/**
 * Class FileHelperTest
 */
class ArrayHelperTest extends TestCase
{

    public function testToArray(): void
    {
        $obj = new \stdClass();
        $objSub = new \stdClass();
        $objSub->version = '2.0';
        $objSub->url = 'https://www.swoft.org';
        $obj->name = 'swoft';
        $obj->description = $objSub;

        //test base
        $arr = ArrayHelper::toArray($obj);
        $this->assertIsArray($arr);
        $this->assertArrayHasKey('description', $arr);

        //test properties
        $arr2 = ArrayHelper::toArray($obj, [get_class($obj) => ['name']]);
        $this->assertSame(['name' => 'swoft'], $arr2);

        //test recursive
        $arr3 = ArrayHelper::toArray($obj, [], false);
        $this->assertIsObject($arr3['description']);
    }

    public function testMerge(): void
    {
        $arr1 = ['a' => 1];
        $arr2 = ['b' => 2];
        $rs = ArrayHelper::merge($arr1, $arr2);
        $this->assertSame(['a' => 1, 'b' => 2], $rs);

        $arr3 = ['a' => 1, 'c' => [1, 2, 3], 'd' => 6];
        $arr4 = ['b' => 2, 'c' => 5];
        $rs2 = ArrayHelper::merge($arr3, $arr4);
        $this->assertSame(1, $rs2['a']);
        $this->assertSame(2, $rs2['b']);
        $this->assertSame(5, $rs2['c']);
        $this->assertSame(6, $rs2['d']);
    }

    public function testGetValue(): void
    {
        //test base
        $arr = ['a' => 1, 'b' => 2];
        $rs = ArrayHelper::getValue($arr, 'b');
        $this->assertSame(2, $rs);

        //test get not exist key
        $rs2 = ArrayHelper::getValue($arr, 'c');
        $this->assertNull($rs2);

        //test default value
        $rs3 = ArrayHelper::getValue($arr, 'c', 3);
        $this->assertSame(3, $rs3);
    }

    public function testRemove(): void
    {
        //test base
        $arr = ['a' => 1, 'b' => 2];
        ArrayHelper::remove($arr, 'a');
        $this->assertSame(['b' => 2], $arr);

        //test get not exist key
        $arr2 = ['a' => 1, 'b' => 2];
        ArrayHelper::remove($arr2, 'c');
        $this->assertSame(['a' => 1, 'b' => 2], $arr2);

        //test return default value
        $rs = ArrayHelper::remove($arr2, 'c', ['a' => 3]);
        $this->assertSame(['a' => 3], $rs);
    }

    public function testExcept(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => ['d' => 1, 'e' => 2]];
        $rs = ArrayHelper::except($arr, ['a']);
        $this->assertSame(['b' => 2, 'c' => ['d' => 1, 'e' => 2]], $rs);

        //test remove key using dot, like 'c.d'
        $rs2 = ArrayHelper::except($arr, ['a', 'b', 'c.d']);
        $this->assertSame(['c' => ['e' => 2]], $rs2);
    }

    public function testForget(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => ['d' => 1, 'e' => 2]];
        ArrayHelper::forget($arr, ['a']);
        $this->assertSame(['b' => 2, 'c' => ['d' => 1, 'e' => 2]], $arr);

        $arr2 = ['a' => 1, 'b' => 2, 'c' => ['d' => 1, 'e' => 2]];
        ArrayHelper::forget($arr2, ['a', 'b', 'c.d']);
        $this->assertSame(['c' => ['e' => 2]], $arr2);
    }

    public function testPull(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => ['d' => 1, 'e' => 2]];
        $rs = ArrayHelper::pull($arr, 'b');
        $this->assertSame(['a' => 1, 'c' => ['d' => 1, 'e' => 2]], $arr);
        $this->assertSame(2, $rs);

        //test default value
        $rs2 = ArrayHelper::pull($arr, 'g', 9);
        $this->assertSame(9, $rs2);
    }


    public function testIndex(): void
    {
        $arr = [
            ['id' => 1, 'data' => 'a'],
            ['id' => 3, 'data' => 'c'],
            ['id' => 3, 'data' => 'a'],
        ];

        $rs = ArrayHelper::index($arr, 'id');
        $this->assertSame([
            1 => ['id' => 1, 'data' => 'a'],
            3 => ['id' => 3, 'data' => 'a'],
        ], $rs);

        //test group with key null
        $rs2 = ArrayHelper::index($arr, null, ['data']);
        $this->assertSame([
            'a' => [
                ['id' => 1, 'data' => 'a'],
                ['id' => 3, 'data' => 'a']
            ],
            'c' => [
                ['id' => 3, 'data' => 'c']
            ],
        ], $rs2);

        //test groups
        $rs3 = ArrayHelper::index($arr, 'id', ['data']);
        $this->assertSame([
            'a' => [
                1 => ['id' => 1, 'data' => 'a'],
                3 => ['id' => 3, 'data' => 'a']
            ],
            'c' => [
                3 => ['id' => 3, 'data' => 'c']
            ],
        ], $rs3);
    }

    public function testGetColumn(): void
    {
        $arr = [
            ['id' => 1, 'data' => 'a'],
            ['id' => 2, 'data' => 'b'],
        ];
        $rs = ArrayHelper::getColumn($arr, 'id');
        $this->assertSame([1, 2], $rs);

        //test callback
        $rs2 = ArrayHelper::getColumn($arr, function ($element) {
            return $element['data'];
        });
        $this->assertSame(['a', 'b'], $rs2);
    }

    public function testMap(): void
    {
        $arr = [
            ['id' => 1, 'data' => 'a', 'group' => 'a'],
            ['id' => 5, 'data' => 'b', 'group' => 'b'],
            ['id' => 3, 'data' => 'c', 'group' => 'a'],
        ];
        $rs = ArrayHelper::map($arr, 'id', 'data');
        $this->assertSame([
            1 => 'a',
            5 => 'b',
            3 => 'c',
        ], $rs);

        //test group
        $rs2 = ArrayHelper::map($arr, 'id', 'data', 'group');
        $this->assertSame([
            'a' => [
                1 => 'a',
                3 => 'c'
            ],
            'b' => [
                5 => 'b'
            ]
        ], $rs2);
    }

    public function testKeyExists(): void
    {
        $arr = ['id' => 1, 'data' => 'a'];
        $rs = ArrayHelper::keyExists('id', $arr);
        $this->assertTrue($rs);

        $rs2 = ArrayHelper::keyExists('name', $arr);
        $this->assertFalse($rs2);
    }

    public function testMultisort(): void
    {
        $arr = [
            ['id' => 1],
            ['id' => 5],
            ['id' => 3],
        ];
        ArrayHelper::multisort($arr, 'id');
        $this->assertSame([
            ['id' => 1],
            ['id' => 3],
            ['id' => 5],
        ], $arr);

    }

    public function testIsAssociative(): void
    {
        $arr = ['id' => 1, 'data' => 'a'];
        $rs = ArrayHelper::isAssociative($arr);
        $this->assertTrue($rs);

        $arr2 = [1 => 1, 'data' => 'a'];
        $rs2 = ArrayHelper::isAssociative($arr2);
        $this->assertFalse($rs2);

        $rs3 = ArrayHelper::isAssociative($arr2, false);
        $this->assertTrue($rs3);
    }

    public function testIsIndexed(): void
    {
        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::isIndexed($arr);
        $this->assertTrue($rs);


        $arr = ['a', 'b', 5 => 'c'];
        $rs = ArrayHelper::isIndexed($arr);
        $this->assertTrue($rs);


        $arr = ['a', 'b', 'key' => 'c'];
        $rs = ArrayHelper::isIndexed($arr);
        $this->assertFalse($rs);

        $arr = ['a', 'b', 5 => 'c'];
        $rs = ArrayHelper::isIndexed($arr, true);
        $this->assertFalse($rs);
    }

    public function testIsIn(): void
    {
        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::isIn('b', $arr);
        $this->assertTrue($rs);

        $rs = ArrayHelper::isIn('d', $arr);
        $this->assertFalse($rs);
    }

    public function testIsTraversable(): void
    {
        $arr = ['a'];
        $rs = ArrayHelper::isTraversable($arr);
        $this->assertTrue($rs);

        $arr = 'a';
        $rs = ArrayHelper::isTraversable($arr);
        $this->assertFalse($rs);
    }

    public function testIsSubset(): void
    {
        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::isSubset(['b', 'c'], $arr);
        $this->assertTrue($rs);

        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::isSubset(['b', 'd'], $arr);
        $this->assertFalse($rs);
    }

    public function testFilter(): void
    {
        $arr = [
            'id' => 1,
            'description' => [
                'name' => 'swoft',
                'version' => '2.0'
            ]
        ];
        $rs = ArrayHelper::filter($arr, ['id', 'description.version']);
        $this->assertSame([
            'id' => 1,
            'description' => [
                'version' => '2.0'
            ]
        ], $rs);
    }

    public function testAccessible(): void
    {
        $arr = ['a'];
        $rs = ArrayHelper::accessible($arr);
        $this->assertTrue($rs);

        $arr = 'a';
        $rs = ArrayHelper::accessible($arr);
        $this->assertFalse($rs);
    }

    public function testExists(): void
    {
        $arr = ['id' => 1, 'name' => 'swoft'];
        $rs = ArrayHelper::exists($arr, 'name');
        $this->assertTrue($rs);
        $rs2 = ArrayHelper::exists($arr, 'description');
        $this->assertFalse($rs2);
    }

    public function testGet(): void
    {
        $arr = ['id' => 1, 'name' => 'swoft'];
        $rs = ArrayHelper::get($arr, 'name');
        $this->assertSame('swoft', $rs);

        $rs2 = ArrayHelper::get($arr, 'description');
        $this->assertNull($rs2);

        $rs3 = ArrayHelper::get($arr, 'description', '2.0');
        $this->assertSame('2.0', $rs3);
    }

    public function testHas(): void
    {
        $arr = [
            'id' => 1,
            'description' => [
                'name' => 'swoft',
                'version' => '2.0'
            ]
        ];
        $rs = ArrayHelper::has($arr, 'id');
        $this->assertTrue($rs);

        $rs2 = ArrayHelper::has($arr, 'name');
        $this->assertFalse($rs2);

        $rs3 = ArrayHelper::has($arr, 'description.name');
        $this->assertTrue($rs3);
    }

    public function testSet(): void
    {
        $arr = ['id' => 1];
        $rs = ArrayHelper::set($arr, 'name', 'swoft');
        $this->assertSame([
            'id' => 1,
            'name' => 'swoft'
        ], $rs);
    }

    public function testInsert(): void
    {
        $arr = ['a', 'b', 'c'];
        ArrayHelper::insert($arr, 2, 'd', 'e');
        $this->assertSame(['a', 'b', 'd', 'e', 'c'], $arr);
    }

    public function testWap(): void
    {
        $arr = ArrayHelper::wrap(['a']);
        $this->assertSame(['a'], $arr);


        $arr2 = ArrayHelper::wrap('a');
        $this->assertSame(['a'], $arr2);
    }


    public function testIsArrayable(): void
    {
        $arr = ['a'];
        $rs = ArrayHelper::isArrayable($arr);
        $this->assertTrue($rs);

        $arr = 'a';
        $rs = ArrayHelper::isArrayable($arr);
        $this->assertFalse($rs);
    }

    public function testFlatten(): void
    {
        $arr = [
            'id' => 1,
            'description' => [
                'name' => 'swoft',
                'version' => '2.0'
            ]
        ];
        $rs = ArrayHelper::flatten($arr);
        $this->assertSame([1, 'swoft', '2.0'], $rs);
    }

    public function testFindSimilar(): void
    {
        $arr = [
            'swoft',
            'swoft-2',
            'yii',
            'thinkphp',
            'test-swoft-cloud'
        ];
        $rs = ArrayHelper::findSimilar('swoft', $arr);
        $this->assertSame([
            'swoft',
            'swoft-2',
            'test-swoft-cloud'
        ], $rs);
    }

    public function testGetKeyMaxWidth(): void
    {
        $arr = [
            'id' => 1,
            'name' => 'swoft',
            'version' => '2.0',
            'description' => 'php framework'
        ];

        $rs = ArrayHelper::getKeyMaxWidth($arr);
        $this->assertSame(strlen('description'), $rs);
    }

    public function testFirst(): void
    {
        $arr = ['a', 'b', 'c', 'd'];
        $rs = ArrayHelper::first($arr, function ($value) {
            return $value == 'c';
        });
        $this->assertSame('c', $rs);
    }

    public function testWhere(): void
    {
        $arr = ['a', 'b', 'c', 'd'];
        $rs = ArrayHelper::where($arr, function ($value) {
            return $value == 'c';
        });
        $this->assertSame([2 => 'c'], $rs);
    }

    public function testQuery(): void
    {
        $arr = [
            'id' => 1,
            'name' => 'swoft',
        ];
        $rs = ArrayHelper::query($arr);
        $this->assertSame('id=1&name=swoft', $rs);
    }

    public function testOnly(): void
    {
        $arr = [
            'id' => 1,
            'name' => 'swoft',
        ];
        $rs = ArrayHelper::only($arr, ['name']);
        $this->assertSame(['name' => 'swoft'], $rs);
    }

    public function testLast(): void
    {
        $arr = ['a', 'b', 'c', 'd'];
        $rs = ArrayHelper::last($arr, function ($value) {
            return $value == 'c';
        });
        $this->assertSame('c', $rs);
    }

    public function testPluck(): void
    {
        $arr = [
            ['id' => 1, 'name' => 'swoft'],
            ['id' => 2, 'name' => 'yii'],
        ];
        $rs = ArrayHelper::pluck($arr, 'name');
        $this->assertSame([
            'swoft', 'yii'
        ], $rs);
    }

    public function testCollapse(): void
    {
        $arr = [
            ['id' => 1, 'name' => 'swoft', 'version' => '2.0'],
            ['id' => 2, 'name' => 'yii'],
        ];
        $rs = ArrayHelper::collapse($arr);
        $this->assertSame([
            'id' => 2,
            'name' => 'yii',
            'version' => '2.0'
        ], $rs);
    }

    public function testCrossJoin(): void
    {
        $arr = ['a'];
        $arr2 = ['b'];

        $rs = ArrayHelper::crossJoin($arr, $arr2);
        $this->assertSame([
           ['a', 'b']
        ], $rs);
    }

    public function testPrepend(): void
    {
        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::prepend($arr, 'd');
        $this->assertSame([
            'd', 'a', 'b', 'c'
        ], $rs);
    }

    public function testRandom(): void
    {
        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::random($arr);
        $this->assertTrue(in_array($rs, $arr));
    }

    public function testShuffle(): void
    {
        $arr = ['a', 'b', 'c'];
        $rs = ArrayHelper::shuffle($arr);
        $this->assertTrue(count($arr) === count($rs));
    }


}
