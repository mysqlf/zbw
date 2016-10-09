<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/12
 * Time: 18:39
 */

namespace Service\Model;


class ServiceProductModel extends  ServiceAdminModel
{
    public function productList($where, $admin)
    {
        $page = I('get.p',1);
        $count = $this->where("state <> -9 AND company_id = {$admin['company_id']} ")->count();
        $result = $this->where("state <> -9 AND company_id = {$admin['company_id']} ")->order('update_time desc')->page($page,20)->select();
        foreach ($result as $key => $value) {         
            $result[$key]['location_num'] = count(json_decode($value['other_location']));
          
        }
        $pageshow = showpage($count,20);
        return array('page'=>$pageshow,'result'=>$result);
    }

    public function productDetail($product,$admin, $person_service_price)
    {
        $result = $this->where("id = '{$product['id']}' AND state <> -9 AND company_id = {$admin['company_id']} ")->find();
        if(IS_POST)
        {
            $product['location'] = M('company_info')->getFieldById($admin['company_id'], 'location');
            if($product['service_price_state'] == 1)//免费
            {
                $product['member_price'] = 0.00;
                $product['service_price_state'] = 0;
            }
            if(!empty($product['member_price']) && $product['service_price_state'] == 0){
                $product['service_price_state'] = 1;
            }

            $otherLocation = explode(',', $product['other_location']);
            $product['other_location'] = json_encode($otherLocation);
            if($product['service_type'] == 2)
            {
                $product['amount'] = round($product['service_price'][0]/3, 2);
                foreach($product['service_price'] as $k=>$v)
                {
                    $product['service_price1'][$k]['service_price'] = $product['service_price'][$k];
                    $product['service_price1'][$k]['validity'] = $product['validity'][$k];
                }
                $product['service_price'] =    $product['service_price1'];
                
            }
            else
            {
                $product['amount'] = $product['service_price'][0];
                $product['service_price'] = array(array('service_price'=>$product['service_price'][0],'validity'=>$product['validity'][0]));
                
            }

            $product['service_price'] = json_encode($product['service_price']);
            unset($product['validity']);
            unset($product['service_price1']);
            
            $serviceProductLocation = D('ServiceProductLocation');
            $serviceProductLocationData = array();
            
 
            if(empty($result))
            {
                $product['create_time'] = date('Y-m-d H:i:s',time());
                $product['update_time'] = date('Y-m-d H:i:s',time());
                $product['create_admin_id'] = $admin['id'];
                unset($product['id']);
                $state = $this->add($product);
                if($state)
                {
	            	//添加服务城市关联表数据
	            	if ($otherLocation) {
			            foreach ($otherLocation as $key => $value) {
			            	$serviceProductLocationData[] = ['service_product_id'=>$state,'location'=>$value];
			            }
		            	$serviceProductLocationAddResult = $serviceProductLocation->addAll($serviceProductLocationData);
	            	}
	            	
                    $this->adminLog($admin['user_id'],'添加产品：'.$product['name'].' 成功');
                    return ajaxJson(0,'添加成功');
                }
                else
                {
                    $this->adminLog($admin['user_id'],'添加产品：'.$product['name'].' 失败');
                    return ajaxJson(-1,'添加失败');
                }
            }
            else
            {
                $product['update_time'] = date('Y-m-d H:i:s',time());
             
                $state = $this->where("id = '{$product['id']}' AND state <> -9")->save($product);
                if($state)
                {
	            	//更新服务城市关联表数据
	            	if ($otherLocation) {
		            	$serviceProductLocationDeleteResult = $serviceProductLocation->where(['service_product_id'=>$product['id']])->delete();
			            foreach ($otherLocation as $key => $value) {
			            	$serviceProductLocationData[] = ['service_product_id'=>$product['id'],'location'=>$value];
			            }
		            	$serviceProductLocationAddResult = $serviceProductLocation->addAll($serviceProductLocationData);
		            }
	            	
                    $this->adminLog($admin['user_id'],'修改产品：'.$result['name'].' 成功'.'，现名称：'.$product['name']);
                    return ajaxJson(0,'修改成功');
                }
                else
                {
                    $this->adminLog($admin['user_id'],'修改产品：'.$result['name'].' 失败');
                    return ajaxJson(-1,'修改失败');
                }
            }
        }
        return $result;
    }

    public function delProduct($product,$admin)
    {
        $result = $this->where("id = '{$product['id']}' AND state <> -9 AND company_id = {$admin['company_id']}")->find();
        if(empty($result)) return ajaxJson(-1,'产品不存在');
        $state = $this->where("id = '{$product['id']}' AND state <> -9 AND company_id = {$admin['company_id']}")->save(array('state'=>-9));
        if($state)
        {
            $this->adminLog($admin['user_id'],'删除产品：'.$result['name'].' 成功');
            return ajaxJson(0,'删除成功');
        }
        else
        {
            $this->adminLog($admin['user_id'],'删除产品：'.$result['name'].' 失败');
            return ajaxJson(-1,'删除失败');
        }
    }

    public function serviceList($admin)
    {
        $m = M('value_added_service');
        $page = I('get.p',1);
        $count = $m->alias('vas')->where("vas.state <> -9 AND vas.company_id = {$admin['company_id']}")->count();
        $result = $m->alias('vas')->field('vas.*,ci.company_name')->where("vas.state <> -9 AND vas.company_id = {$admin['company_id']}")
                    ->join('zbw_company_info ci ON ci.id=vas.company_id')
                    ->order('create_date desc')->page($page,20)->select();
        $pageshow = showpage($count,20);
        return array('page'=>$pageshow,'result'=>$result);
    }

    public function serviceDetail($service,$admin)
    {
        $m = M('value_added_service');
        $result = $m->where("id = '{$service['id']}' AND state <> -9 AND company_id = {$admin['company_id']}")->find();
        if(IS_POST)
        {
            if(empty($result))
            {
                $service['update_date'] = date('Y-m-d H:i:s',time());
                $service['create_date'] = date('Y-m-d H:i:s',time());
                $service['admin_id'] = $admin['id'];
                unset($service['id']);
                $state = $m->add($service);
                if($state)
                {
                    $this->adminLog($admin['user_id'],'添加增值服务：'.$service['product_name'].' 成功');
                    return ajaxJson(0,'添加成功');
                }
                else
                {
                    $this->adminLog($admin['user_id'],'添加增值服务：'.$service['product_name'].' 失败');
                    return ajaxJson(-1,'添加失败');
                }
            }
            else
            {
                $service['update_date'] = date('Y-m-d H:i:s',time());
                $state = $m->where("id = '{$service['id']}' AND state <> -9")->save($service);
                if($state)
                {
                    $this->adminLog($admin['user_id'],'修改增值服务：'.$result['product_name'].' 成功'.'，现名称：'.$service['product_name']);
                    return ajaxJson(0,'修改成功');
                }
                else
                {
                    $this->adminLog($admin['user_id'],'修改增值服务：'.$result['product_name'].' 失败');
                    return ajaxJson(-1,'修改失败');
                }
            }
        }
        return $result;
    }
    
    public function delService($service,$admin)
    {
        $m = M('value_added_service');
        $result = $m->where("id = '{$service['id']}' AND state <> -9 AND company_id = {$admin['company_id']}")->find();
        if(empty($result)) return ajaxJson(-1,'增值服务不存在');
        $state = $m->where("id = '{$service['id']}' AND state <> -9 AND company_id = {$admin['company_id']}")->save(array('state'=>-9));
        if($state)
        {
            $this->adminLog($admin['user_id'],'删除增值服务：'.$result['product_name'].' 成功');
            return ajaxJson(0,'删除成功');
        }
        else
        {
            $this->adminLog($admin['user_id'],'删除增值服务：'.$result['product_name'].' 失败');
            return ajaxJson(-1,'删除失败');
        }
    }
    
	/**
	 * getAllEffectiveServiceProductOrderLocation function
	 * 获取所有有效订单的参保地
	 * @param int $companyId 企业信息ID
	 * @return array
	 * @author rohochan <rohochan@gmail.com>
	 **/
	public function getAllEffectiveServiceProductOrderLocation($companyId = 0){
		if ($companyId > 0) {
			$serviceProductResult = $this->field(true)->where(array('company_id'=>$companyId))->select();
			$productIdArray = array();
			if ($serviceProductResult) {
				foreach ($serviceProductResult as $key => $value) {
					$productIdArray[] = $value['id'];
				}
			}
			if ($productIdArray) {
				//$productIdArray = array();
				//确认付款以及(服务中或服务结束)
				$serviceProductOrder = M('ServiceProductOrder');
				//$condition = array('product_id'=>array('in',$productIdArray),'state'=>1,'service_state'=>array('in','2,3'));
				$condition = array('product_id'=>array('in',$productIdArray),'service_state'=>array('in','2,3'));
				$result = $serviceProductOrder->field('id')->where($condition)->select();
				
				if ($result) {
					$serviceProductOrderIdArray = array();
					foreach ($result as $key => $value) {
						$serviceProductOrderIdArray[$value['id']] = $value['id'];
					}
					$result = array();
					$warrantyLocationResult = $this->table(C('DB_PREFIX').'warranty_location')->field('id,location')->where(array('service_product_order_id'=>array('in',$serviceProductOrderIdArray),'state'=>0))->select();
					if ($warrantyLocationResult) {
						foreach ($warrantyLocationResult as $key => $value) {
							//$result[$value['location']] = showAreaName($value['location']);
							$value['location'] = ($value['location']/1000<<0)*1000;
							$result[$value['location']] = showAreaName($value['location']);
						}
					}
					return $result;
				}else if (null === $result) {
					return $result;
				}else if (false === $result) {
					wlog($this->getDbError());
					$this->error = $this->getDbError();
					return false;
				}else {
					$this->error = '未知错误！';
					return false;
				}
			}else {
				$this->error = '产品数据错误！';
				return false;
			}
		}else {
			$this->error = '非法参数！';
			return false;
		}
	}
	
}