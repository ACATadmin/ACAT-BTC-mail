<?php
/**
 * 获取并设置订单状态
 * @param unknown $order_id
 */
function getNextMonthDay($start_day,$month_length,$last_day=false)
{
    if($month_length==0)
    {
        return $start_day;
    }
    
    $year = substr($start_day,0,4);
    $month = substr($start_day,5,2);
    $day = substr($start_day,8,2);
    
    $month_list = array(
        array(
            'month'=>'01',
            'index'=>0,
        ),
        array(
            'month'=>'02',
            'index'=>1,
        ),
        array(
            'month'=>'03',
            'index'=>2,
        ),
        array(
            'month'=>'04',
            'index'=>3,
        ),
        array(
            'month'=>'05',
            'index'=>4,
        ),
        array(
            'month'=>'06',
            'index'=>5,
        ),
        array(
            'month'=>'07',
            'index'=>6,
        ),
        array(
            'month'=>'08',
            'index'=>7,
        ),
        array(
            'month'=>'09',
            'index'=>8,
        ),
        array(
            'month'=>'10',
            'index'=>9,
        ),
        array(
            'month'=>'11',
            'index'=>10,
        ),
        array(
            'month'=>'12',
            'index'=>11,
        ),
    );
    $month_format = getIdIndexArr($month_list,'month');
    
    $month_index = $month_format[$month]['index'];

    $year_target = intval($year);
    $i = $month_index+$month_length+1;
    $j = $month_index+$month_length;
    for(;$i>12;$i-=12)
    {
        $year_target++;
    }
    for(;$j>11;$j-=12)
    {
    }
    $month_target = $month_list[$j]['month'];
    if(($day=='29')||($day=='30'))
    {
        if($month_target=='02')
        {
            if($year_target%4==0)
            {
                $day_target = '29';
            }
            else
            {
                $day_target = '28';
            }
        }
        else
        {
            $day_target = $day;
        }
    }
    else if($day=='31')
    {
        if($month_target=='02')
        {
            if($year_target%4==0)
            {
                $day_target = '29';
            }
            else
            {
                $day_target = '28';
            }
        }
        else if(($month_target=='04')||($month_target=='06')||($month_target=='09')||($month_target=='11'))
        {
            $day_target = '30';
        }
        else
        {
            $day_target = $day;
        }
    }
    else
    {
        $day_target = $day;
    }
    if($last_day)
    {
        $day = $year_target.'-'.$month_target.'-'.$day_target;
        $last_day = date('Y-m-d',  strtotime($day)-24*3600);
        return $last_day;
    }
    return $year_target.'-'.$month_target.'-'.$day_target;
    
}













