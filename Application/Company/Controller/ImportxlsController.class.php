<?php
/**
* 
*/
namespace Company\Controller;
class ImportxlsController extends HomeController
{   

    
    private $xlspath;
    public function setxlspath($path){
        $this->xlspath=$path;
    }
    public function getxlspath(){
        return $this->xlspath;
    }
   
    /**
     * [gethz 获取文件后缀]
     * @param  [str] $filename [文件名]
     * @return [str]           [后缀]
     */
    public function gethz($filename){
        $hz=explode('.',$filename);
        return $hz[count($hz)-1];
    }
   
    /**
     * [get_value 获取值]
     * @param  [type] $currentSheet [对象]
     * @param  [type] $key          [下标]
     * @param  [type] $currentRow   [列数]
     * @return [type]               [值]
     */
    public function get_value($currentSheet,$key,$currentRow){
        return $currentSheet->getCellByColumnAndRow(ord($key) - 65,$currentRow)->getValue(); 
    }
    /**
     * [getrule description]获取模板规则,rule
     * @return [type] [description]
     */
    public function getrule(){
        header('Content-Type: text/html; charset=UTF-8');
        $m=M('service_product_order');
        $where=array('user_id'=>$this->mCid,'service_state'=>2);
        $Options=$m->field('template_id,product_id')->where($where)->select();
        $p=M('service_product');
        foreach ($Options as $key => $value) {
            $where=array('id'=>$value['product_id']);
            $pro_name=$p->field('name')->where($where)->select();
            $Options[$key]['name']=$pro_name[0]['name'];
        }
        $this->assign('Options',$Options);
        $this->display();
    }
    /**
     * [makexls 生成excel]
     * @return [type] [description]
     */
    public function makexls(){
        header('Content-Type: text/html; charset=UTF-8');
        $template_id=I('param.template_id');
        $m=M('template_classify');
        $where='template_id='.$template_id.'';
        $type=$m->field('name,type,fid,id')->where($where)->select();
        $result=self::combinationrule($type);//组合规则
        /*开始制表*/
        $objExcel=setExcelHead();  
        vendor('PHPExcel.PHPExcel.Reader.Excel5');
        $objWriter = new \PHPExcel_Writer_Excel5($objExcel);
         //设置全局水平垂直居中
        $objExcel -> getDefaultStyle() -> getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER) -> setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        /*开始设置表头*/        
        $objExcel->setActiveSheetIndex(0)
                ->setCellValue('A1','姓名')
                ->setCellValue('B1','性别')
                ->setCellValue('C1','手机号')
                ->setCellValue('D1','身份证号码')
                ->setCellValue('E1','会员产品')
                ->setCellValue('F1','参保地')
                ->setCellValue('G1','社保类型');
        $objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(23);
        $objExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        $objExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
        foreach ($result as $key => $value) {
            if ($key==0) {
                $i=1;
                foreach ($result[$key] as $k => $v) {
                    $objExcel->setActiveSheetIndex(0)->setCellValue(chr(ord('G')+$i).'1',$v);
                    $i++;
                }
            }
        }
        /*循环将选项填入表格*/
        $objExcel->setActiveSheetIndex(0);  
        $objActSheet = $objExcel->getActiveSheet();  
        $this->n=0;
        for ($i=2; $i <500 ; $i++) {//无法直接设置一列全都是选项,用循环生成五百行,可修改
            $this->n=1;
            foreach ($result as $key => $value) {
                if ($key==0) {
                   foreach ($result[$key] as $k => $v) {
                        $row=chr(ord('G')+$this->n);
                        $excelva=implode(',',$result[$k]);//选项值
                        $excelva="\"".$excelva."\"";
                        $objValidation = $objActSheet->getCell($row.$i)->getDataValidation(); //这一句为要设置数据有效性的单元格  
                        $objValidation -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)  
                           -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)  
                           -> setAllowBlank(false)  
                           -> setShowInputMessage(true)  
                           -> setShowErrorMessage(true)  
                           -> setShowDropDown(true)  
                           -> setErrorTitle('输入的值有误')  
                           -> setError('您输入的值不在选项内.')  
                           -> setPromptTitle($v)  
                           -> setFormula1($excelva);
                        $this->n++;  
                    }
                }else{
                    continue;
                }  
            }
        }
        //后面几列
        $row=chr(ord('G')+$this->n);
        $i=0;
        $objExcel->setActiveSheetIndex(0)       
                ->setCellValue(chr(ord($row)+$i++).'1','社保基数')
                ->setCellValue(chr(ord($row)+$i++).'1','社保起缴')
                ->setCellValue(chr(ord($row)+$i++).'1','公积金基数')
                ->setCellValue(chr(ord($row)+$i++).'1','公积金起缴')
                ->setCellValue(chr(ord($row)+$i++).'1','单位比例')
                ->setCellValue(chr(ord($row)+$i++).'1','个人比例');
        
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $fileName="批量导入报增数据模板".'.xls';
        set_filename_header($ua,$fileName);
        $objWriter->save('php://output');
    }
    /**
     * [Darr_to_Oarr 将rule组合]
     * @param [type] $arr [description]
     */
    public function combinationrule($arr){
        $result=array();
        foreach ($arr as $key => $value) {
            $tmp=$value['fid'];
            if ($value['fid']==0) {
                $result[$tmp][$value['id']]=$value['name'];
            }else{
                $result[$tmp][]=$value['name'];
            }
            
        }
        return $result;
    }
   

   
}
?>