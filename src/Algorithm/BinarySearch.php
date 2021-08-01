<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/6/20
 * Time: 10:54
 */

namespace algorithm;

class BinarySearch
{
    /**
     * 使用循环实现, 返回index
     * @param array $findDate
     * @param int $searchVal
     * @return int
     */
    public static  function binarySearch(array $findDate, int $searchVal)
    {
        $count = count($findDate);
        if($count<1){
            return -1;
        }
        $low = 0;
        $high = $count - 1;
        while ($high >= $low){
            $mid = (int)(($low+$high)/2);
            if ($findDate[$mid] == $searchVal){
                return $mid;
            }
            if($findDate[$mid] > $searchVal){
                $high = $mid-1;
            }
            if($findDate[$mid] < $searchVal){
                $low = $mid+1;
            }
        }
        return -1;
    }

    /**
     * 递归实现
     * 终止条件：找到了值，或者low>high
     * @param array $searchDate
     * @param int $searchVal
     * @param int $low
     * @param int $high
     * @return int
     */
    public static function binarySearchInternally(array $searchDate, int $searchVal, int $low, int $high)
    {
        if($low>$high){
            return -1;
        }

        $mid = (int)(($low + $high)/2);

        if($searchDate[$mid] == $searchVal){
            return $mid;
        }

        if($searchDate[$mid] > $searchVal){
            $high = $mid-1;
            return binarySearchInternally($searchDate, $searchVal, $low, $high);
        }

        if($searchDate[$mid] < $searchVal){
            $low = $mid+1;
            return binarySearchInternally($searchDate, $searchVal, $low, $high);
        }
    }

}