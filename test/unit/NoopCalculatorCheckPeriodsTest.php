<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\NoopCalculator;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;

/**
 * Class TestNoopCalculator
 *
 * @package Tests\unit
 */
class NoopCalculatorCheckPeriodsTest extends TestCase
{

    public function testCountPeriods(): void
    {
        $arrayCases = $this->getCases();

        $function  = new \ReflectionMethod( 'Statistics\Calculator\NoopCalculator', 'getUsedMonths' );
        $function->setAccessible(true);

        foreach ( $arrayCases as $case ){
            $objParamTo = new ParamsTo();
            $objParamTo->setStartDate( new \DateTime( $case[0] ) );
            $objParamTo->setEndDate( new \DateTime( $case[1] ) );
            $objParamTo->setStatName('Unit test');

            $objCalculator = new NoopCalculator();
            $objCalculator->setParameters( $objParamTo );

            $usedMonths = $function->invoke( $objCalculator, $objParamTo );

            $this->assertSame( count( $usedMonths ), intval( $case[2] ) );
        }
    }

    public function getCases(){
        $arrayCases = [
            // start period, end period, count months
            ['2023-01-01 00:00:00', '2023-01-01 23:59:59', 1],
            ['2022-12-01 00:00:00', '2023-01-01 23:59:59', 2],
            ['2022-11-01 00:00:00', '2023-01-01 23:59:59', 3],
            ['2023-01-01 00:00:00', '2025-01-01 23:59:59', 25],
            ['2023-02-01 00:00:00', '2023-01-01 23:59:59', 1],
        ];

        return $arrayCases;

    }
}
