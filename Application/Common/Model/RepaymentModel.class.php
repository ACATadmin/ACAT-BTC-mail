<?php
namespace Common\Model;

/**
 * 生成多层树状下拉选框的工具模型
 */
class RepaymentModel extends BaseModel
{
    
    protected $autoCheckFields = false;
    
    protected function _initialize() {}
    
    protected $option = array();
    
    public function set($name,$value)
    {
        $option[$name] = $value;        
    }
    
    public function get($name)
    {
        return $option[$name];
    }
    
    public function getRepaymentList($para=array())
    {
        $ret['status'] = 1;
        $ret['msg'] = '';
        $method = $para['method'];
        if(!$method)
        {
            $ret['status'] = 0;
            $ret['msg'] = '必须选择还款方式';
            return $ret;
        }
        $month = $para['month'];  //贷款月数
        
        $start_day = $para['start_day'];  //开始贷款日期
        $end_day = $para['end_day'];  //开始贷款日期
        $loanamount = $para['loanamount'];
        if(!$loanamount)
        {
            $ret['status'] = 0;
            $ret['msg'] = '必须传入保理金额';
            return $ret;
        }
        $rate = $para['rate'];
        if(!$rate)
        {
            $ret['status'] = 0;
            $ret['msg'] = '必须传入保理费率';
            return $ret;
        }
        switch($method)
        {
            case 'XXHB':
                $info['loan'] = $para;
                //$interest = ($loanamount*($rate/100))/365;
                //$interest = round($interest,2);
                
                $interest = ($loanamount*($rate/100));
                
                $exit = 0;
                for($i=0;$exit==0;$i++)
                {
                    $repay = array();
                    $repay['start_day'] = getNextMonthDay($start_day, $i);
                    $repay['repayment_day'] = getNextMonthDay($start_day, $i+1);
                    $repay['end_day'] = date('Y-m-d',strtotime($repay['repayment_day'])-24*3600);
                    if($end_day<=$repay['end_day'])
                    {
                        $repay['end_day'] = $end_day;
                        $exit = 1;
                    }
                    $repay['repayment_day'] = $repay['end_day'];
                    $day_length = (strtotime($repay['end_day']) - strtotime($repay['start_day']))/(24*3600);
                    $repay['days'] = $day_length+1;
                    
                    $repay['day_interest'] = $interest/365;
                    $repay['interest'] = $interest*$repay['days']/365;
                    $repay['day_interest'] = round($repay['day_interest'],2);
                    $repay['interest'] = round($repay['interest'],2);
                    
                    $repay['principal'] = $exit?$loanamount:0;
                    $repay['pay_money'] = $repay['interest'] + $repay['principal'];
                    $repaymentList[] = $repay;
                    $repayment['interest'] += $repay['interest'];
                }
                /* for($i=0;$i<$month;$i++)
                {
                    $repay = array();
                    $repay['start_day'] = getNextMonthDay($start_day, $i);
                    $repay['repayment_day'] = getNextMonthDay($start_day, $i+1);
                    $repay['end_day'] = date('Y-m-d',strtotime($repay['repayment_day'])-24*3600);
                    $repay['repayment_day'] = $repay['end_day'];
                    $day_length = (strtotime($repay['end_day']) - strtotime($repay['start_day']))/(24*3600);
                    $repay['days'] = $day_length+1;
                    $repay['day_interest'] = $interest;
                    $repay['interest'] = $repay['day_interest']*$repay['days'];
                    $repay['principal'] = (($i+1)==$month)?$loanamount:0;
                    $repay['pay_money'] = $repay['interest'] + $repay['principal'];
                    $repaymentList[] = $repay;
                    $repayment['interest'] += $repay['interest'];
                } */
                $info['repayment'] = $repayment;
                $info['repaymentList'] = $repaymentList;
                $ret['info'] = $info;
                break;
            case 'YCXBX':
                $info['loan'] = $para;
//                $interest = ($loanamount*($rate/100))/365;
//                $interest = round($interest,2);
                
                $interest = ($loanamount*($rate/100));
                
                $repay = array();
                $repay['start_day'] = $start_day;
                //$repay['repayment_day'] = getNextMonthDay($start_day, $month);
                //$repay['end_day'] = date('Y-m-d',strtotime($repay['repayment_day'])-24*3600);
                $repay['end_day'] = $end_day;
                $repay['repayment_day'] = $repay['end_day'];
                $day_length = (strtotime($repay['end_day']) - strtotime($repay['start_day']))/(24*3600);
                $repay['days'] = $day_length+1;
                
                $repay['day_interest'] = $interest/365;
                $repay['interest'] = $interest*$repay['days']/365;
                $repay['day_interest'] = round($repay['day_interest'],2);
                $repay['interest'] = round($repay['interest'],2);
                
                $repay['principal'] = $loanamount;
                $repay['pay_money'] = $repay['interest'] + $repay['principal'];
                $repaymentList[] = $repay;
                $repayment['interest'] += $repay['interest'];
                $info['repayment'] = $repayment;
                $info['repaymentList'] = $repaymentList;
                $ret['info'] = $info;
                break;
            default:
                break;
        }
        
        return $ret;        
        
    }
    
}
?>
