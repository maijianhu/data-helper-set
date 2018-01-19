<?php
/**
 * DataHelperSet
 */
use m35\DataHelperSet;
use m35\DataHelperSetFormatter;

class TestBench
{
    /**
     * @Revs(1000)
     */
    public function benchTest1()
    {
        $string = 'hello world';
        return DataHelperSet::format($string, DataHelperSetFormatter::TYPE_INT);
    }

    /**
     * @Revs(1000)
     */
    public function benchTest2()
    {
        $string = 'hello world';
        return (int) $string;
    }
}