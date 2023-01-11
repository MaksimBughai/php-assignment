<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class NoopCalculator extends AbstractCalculator
{

    protected const UNITS = 'posts';

    /**
     * array of count posts per author in selected period
     * @var array
     */
    private $countPostsPerAuthor = [];

    /**
     * array which has info about author_id => author_name
     * @var array
     */
    private $authorsName = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $authorId = $postTo->getAuthorId();
        $authorName = $postTo->getAuthorName();

        if( !isset( $this->authorsName[ $authorId ] ) ){
            $this->authorsName[ $authorId ] = $authorName;
        }

        $this->countPostsPerAuthor[ $authorId ]  =  ($this->countPostsPerAuthor[ $authorId ] ?? 0) + 1;
    }

    /**
     * function for getting months from selected period
     *
     * we need to see all months from period because we may have case when in months we don`t any have posts
     */
    protected function getUsedMonths(): array
    {
        $usedMonths = [];

        $startDate = $this->parameters->getStartDate();
        $endDate = $this->parameters->getEndDate();

        $inPeriod = true;
        while( $inPeriod ){
            $month = $startDate->format('Y-m');
            $usedMonths[ $month ] = $month;

            $startDate->modify('+1 month');
            if( $startDate->getTimestamp() > $endDate->getTimestamp() ){
                $inPeriod = false;
            }
        }

        return $usedMonths;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        $usedMonths = $this->getUsedMonths();

        // if we don`t have posts in selected period
        if( !count( $this->countPostsPerAuthor )
            || !count( $usedMonths ) ){
            return $stats;
        }

        $countMonthInPeriod = max( count( $usedMonths ), 1 );

        foreach ($this->countPostsPerAuthor as $authorId => $totalPosts) {
            $authorName = $this->authorsName[ $authorId ] ?? $authorId;
            $averagePosts = ceil( $totalPosts/$countMonthInPeriod );

            $child = (new StatisticsTo())
                ->setName( $authorName )
                ->setSplitPeriod( $authorName )
                ->setValue( $averagePosts )
                ->setUnits(self::UNITS );
            $stats->addChild($child);
        }

        return $stats;
    }
}
