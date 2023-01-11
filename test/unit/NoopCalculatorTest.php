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
class NoopCalculatorTest extends TestCase
{
    // when we have one author in 1 months
    public function testOneAuthorInOneMonth(): void
    {
        $objParamTo = new ParamsTo();
        $objParamTo->setStartDate( new \DateTime( '2023-01-01 00:00:00' ) );
        $objParamTo->setEndDate( new \DateTime( '2023-01-01 23:59:59' ) );
        $objParamTo->setStatName('Unit test');

        $objCalculator = new NoopCalculator();
        $objCalculator->setParameters( $objParamTo );


        $arrayForInit = [
            ['user_1', '2023-01-01 00:00:00'],
            ['user_1', '2023-01-01 00:00:00'],
            ['user_1', '2023-01-01 00:00:00'],
        ];
        $this->initDataFromArray( $objCalculator, $arrayForInit );

        /** @var StatisticsTo $result */
        $objStatistics = $objCalculator->calculate();

        // count users
        $this->assertSame( count( $objStatistics->getChildren() ), 1 );

        foreach ( $objStatistics->getChildren() as $child ){
            $this->assertSame( $child->getName() , 'user_1' );
            $this->assertSame( $child->getValue() , floatval('3') );
        }
    }

    // when we have 1 author with posts in 2 months
    public function testOneAuthorInTwoMonths(): void
    {
        $objParamTo = new ParamsTo();
        $objParamTo->setStartDate( new \DateTime( '2023-01-01 00:00:00' ) );
        $objParamTo->setEndDate( new \DateTime( '2023-02-01 23:59:59' ) );
        $objParamTo->setStatName('Unit test');

        $objCalculator = new NoopCalculator();
        $objCalculator->setParameters( $objParamTo );


        $arrayForInit = [
            ['user_1', '2023-01-01 00:00:00'],
            ['user_1', '2023-02-01 00:00:00'],
            ['user_1', '2023-02-01 00:00:00'],
        ];
        $this->initDataFromArray( $objCalculator, $arrayForInit );

        /** @var StatisticsTo $result */
        $objStatistics = $objCalculator->calculate();

        // count users
        $this->assertSame( count( $objStatistics->getChildren() ), 1 );

        foreach ( $objStatistics->getChildren() as $child ){
            $this->assertSame( $child->getName() , 'user_1' );
            $this->assertSame( $child->getValue() , floatval('2') );
        }
    }

    // when we have 1 author and period ith 4 month AND in one of them author didn`t add posts
    public function testOneAuthorInFourMonthsOneEmpty(): void
    {
        $objParamTo = new ParamsTo();
        $objParamTo->setStartDate( new \DateTime( '2023-01-01 00:00:00' ) );
        $objParamTo->setEndDate( new \DateTime( '2023-04-01 23:59:59' ) );
        $objParamTo->setStatName('Unit test');

        $objCalculator = new NoopCalculator();
        $objCalculator->setParameters( $objParamTo );

        // case when we have 3 months with posts and 1 without
        $arrayForInit = [
            ['user_1', '2023-01-01 00:00:00'],
            ['user_1', '2023-02-01 00:00:00'],
            ['user_1', '2023-02-01 00:00:00'],
            ['user_1', '2023-02-01 00:00:00'],
            ['user_1', '2023-04-01 00:00:00'],
            ['user_1', '2023-04-01 00:00:00'],
            ['user_1', '2023-04-01 00:00:00'],
            ['user_1', '2023-04-01 00:00:00'],
            ['user_1', '2023-04-01 00:00:00'],
        ];
        $this->initDataFromArray( $objCalculator, $arrayForInit );

        /** @var StatisticsTo $result */
        $objStatistics = $objCalculator->calculate();

        // count users
        $this->assertSame( count( $objStatistics->getChildren() ), 1 );

        foreach ( $objStatistics->getChildren() as $child ){
            $this->assertSame( $child->getName() , 'user_1' );
            $this->assertSame( $child->getValue() , floatval('3') );
        }
    }

    // when we have 2 authors in one month
    public function testTwoAuthorsInOneMonth(): void
    {
        $objParamTo = new ParamsTo();
        $objParamTo->setStartDate( new \DateTime( '2023-01-01 00:00:00' ) );
        $objParamTo->setEndDate( new \DateTime( '2023-01-01 23:59:59' ) );
        $objParamTo->setStatName('Unit test');

        $objCalculator = new NoopCalculator();
        $objCalculator->setParameters( $objParamTo );


        $arrayForInit = [
            ['user_1', '2023-01-01 00:00:00'],
            ['user_1', '2023-01-01 00:00:00'],
            ['user_1', '2023-01-01 00:00:00'],
            ['user_2', '2023-01-01 00:00:00'],
        ];
        $this->initDataFromArray( $objCalculator, $arrayForInit );

        /** @var StatisticsTo $result */
        $objStatistics = $objCalculator->calculate();

        // count users
        $this->assertSame( count( $objStatistics->getChildren() ), 2 );
    }

    public function initDataFromArray( NoopCalculator &$objCalculator, array $data ){
        foreach ( $data as $row ){
            $objData = new SocialPostTo();
            $objData->setAuthorId( $row[0] );
            $objData->setAuthorName( $row[0] );
            $objData->setDate( new \DateTime($row[1]) );

            $objCalculator->accumulateData( $objData );
        }
    }
}
