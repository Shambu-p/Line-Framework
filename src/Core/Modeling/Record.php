<?php
/**
 * Created by PhpStorm.
 * User: Abnet
 * Date: 2/28/2021
 * Time: 2:46 PM
 */

namespace Absoft\Line\Core\Modeling;


class Record
{

    /**
     * @var array
     * this filed holds the record data as associative array
     */
    private $record_array;

    /**
     * @var int
     * this will hold the maximum number of records to be shown
     */
    private $limit;

    /**
     * @var int
     * this field holds constant to be used in getOne method.
     * this field means getOne method will get the first record
     */
    public static $FIRST = 1;

    /**
     * @var int
     * this field holds constant to be used in getOne method.
     * this field means getOne method will get the last record.
     */
    public static $LAST = 0;



    /**
     * Record constructor.
     * @param array $array
     */
    function __construct(array $array){

        $this->record_array = $array;
        $this->limit = sizeof($array);

    }

    /**
     * this method will return object of this class with
     * single associative array of this record_array field.
     * it will use the method getFirst and getLast.
     *
     * @param integer $place
     * this parameter can be the static fields first and last
     * @return array
     */
    function getOne($place = 1){

    }

    /**
     * @param integer $limit
     * @return $this
     * this will limit the number of records to be returned
     * by default the limit is the maximum number of records.
     */
    function limit(int $limit){

        if($limit >= 0){
            $this->limit = $limit;
        }

        return $this;

    }

    /**
     * @return array
     * this method will return the first record of the record array
     */
    function getFirst(){

    }

    /**
     * @return array
     * this will return the last record of the record array
     */
    function getLast(){

    }

    /**
     * @param $order
     * @param string $type
     *
     * this method will return this object
     * after sorting the record array.
     * this method use different sorting algorithms
     * by default it use selection sorting.
     */
    function sort($order, $type="selection"){

    }

    /**
     * this method implement selection sort algorithm
     * for sorting the input array.
     * and it will return sorted array
     * @param array $array
     * @param int $determiner
     * this parameter determines in which order the array should be sorted. ascending or descending
     * @return void
     */
    private function insertionSort(array $array, $determiner = 1){

    }

    /**
     * this method implements insertion sorting algorithm
     * for sorting the input array.
     * and it will return sorted array
     * @param array $array
     * @param int $determiner
     * @return void
     */
    private function selectionSort(array $array, $determiner = 1){

    }

    /**
     * @param array $array
     * @param int $determiner
     */
    private function mergeSort(array $array, $determiner = 1){

    }

}
