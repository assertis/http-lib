<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class BatchResultsTest extends TestCase
{

    public function testIterationBehaviour()
    {
        $obj1 = new StdClass();
        $obj3 = new StdClass();
        $obj2 = new StdClass();
        $batchRes = new \Assertis\Http\Response\BatchResults([
            $obj1,
            $obj2,
            $obj3,
        ]);

        foreach ($batchRes as $obj){
            \PHPUnit\Framework\Assert::assertInstanceOf(StdClass::class, $obj);
        }
    }

}