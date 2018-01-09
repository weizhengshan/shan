<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->display();
    }

    public function arr(){
        $arr1 = array('a'=>'1','B'=>'2','C'=>3);
        $data = array_change_key_case($arr1,CASE_UPPER);
        // 错误／异常 : 输入值（array）不是一个数组，就会抛出一个错误警告（E_WARNING）
        // $input_array = array("FirSt" => 1, "SecOnd" => 4);
        // [1] array 需要操作的数组
        // [2] CASE_UPPER 或 CASE_LOWER（默认值）
        // print_r(array_change_key_case($input_array, CASE_UPPER));
        // [ ['FIRST' => 1], ['SECOND' => 4] ]

        dd($data);
    }
}