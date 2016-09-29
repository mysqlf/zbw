var Mock = require('mockjs');

// 企业订单详情
Mock.mock('/Order-comOrderDetail', {
	'status': '@integer(0,1)',
    'msg': '@cname',
    'data': []
})


Mock.mock('/Order-payrol', {
	'status': '0',
    'msg': '@cname',
    'data': [{
    	'username': '@cname',
    	'card_num': '6221886242006433786',
    	'bank':'上海银行',
    	branch: '浦东支行',
    	account: '6221886242006433786',
    	date: '2015121',
    	wages: '2000',
    	deduction_income_tax: '200',
    	deduction_social_insurance: '100',
    	deduction_provident_fund: '200',
    	replacement: '120',
    	deduction_order: '10',
    	state: '1'
    }]
})

// 社保比例生成
function sbScale(){
	var str = '';

	if(Math.random(10) > .5){
		str = '+' + Mock.mock('@integer(0,100)');
	}
	return Mock.mock('@integer(0,10)') + '%' + str;
}
// 参保信息
Mock.mock('/Order-socialSecurity', {
	'status': '0',
    'msg': '@cname',
    'data': [{
    "sb_classify": [
        {
            "id": "1",
            "name": "户口性质",
            "type": "1",
            "template_id": "1",
            "fid": "0",
            "create_time": "2016-03-11 11:09:20",
            "child": [
                {
                    "id": "4",
                    "name": "本地城镇",
                    "type": "1",
                    "template_id": "1",
                    "fid": "1",
                    "create_time": "2016-03-11 11:09:25"
                },
                {
                    "id": "5",
                    "name": "本地农村",
                    "type": "1",
                    "template_id": "1",
                    "fid": "1",
                    "create_time": "2016-03-11 11:09:28"
                },
                {
                    "id": "6",
                    "name": "外地城镇",
                    "type": "1",
                    "template_id": "1",
                    "fid": "1",
                    "create_time": "2016-03-11 11:09:31"
                },
                {
                    "id": "7",
                    "name": "外地农村",
                    "type": "1",
                    "template_id": "1",
                    "fid": "1",
                    "create_time": "2016-03-11 11:09:33"
                }
            ]
        },
        {
            "id": "2",
            "name": "性别",
            "type": "1",
            "template_id": "1",
            "fid": "0",
            "create_time": "2016-03-11 11:09:20",
            "child": [
                {
                    "id": "8",
                    "name": "男",
                    "type": "1",
                    "template_id": "1",
                    "fid": "2",
                    "create_time": "2016-03-11 11:09:37"
                },
                {
                    "id": "9",
                    "name": "女",
                    "type": "1",
                    "template_id": "1",
                    "fid": "2",
                    "create_time": "2016-03-11 11:09:39"
                }
            ]
        }
    ],
    "gzj_classify": [
        {
            "id": "3",
            "name": "姓氏",
            "type": "2",
            "template_id": "1",
            "fid": "0",
            "create_time": "2016-03-11 11:09:22",
            "child": [
                {
                    "id": "10",
                    "name": "张",
                    "type": "1",
                    "template_id": "1",
                    "fid": "3",
                    "create_time": "2016-03-11 11:09:41"
                },
                {
                    "id": "11",
                    "name": "赵",
                    "type": "1",
                    "template_id": "1",
                    "fid": "3",
                    "create_time": "2016-03-11 11:09:44"
                }
            ]
        }
    ],
    "sb_rule": {
    "min": '@integer(500,1000)',
    "max": '@integer(1000,1500)',
    "pro_cost": '@integer(50,150)',
    "material": "1所需材料列表内容2",
    "items|5": [
        {
            "name": "@cname",
            "rules": {
                "company": sbScale,
                "person": sbScale
            }
        }
    ]
	},
    "gzj_rule": {
        "min": "1000.00",
        "max": "2000.00",
        "company": "5%,8%,7%",
        "person": "8%-9%",
        "pro_cost": "20.00"
    }
}]
})



Mock.mock('/Order-socialSecurity2', {
    "status": 0,
    "msg": "操作成功",
    "data": {
        "养老保险": {
            "person": {
                "scale": "5%",
                "scaleSum": 50.6,
                "fixedSum": 0,
                "sum": 50.6
            },
            "company": {
                "scale": "6%",
                "scaleSum": 60.72,
                "fixedSum": 4,
                "sum": 64.72
            },
            "total": 115.32,
            "pro_cost": "15.00"
        },
        "医疗保险": {
            "person": {
                "scale": "5%",
                "scaleSum": 50.6,
                "fixedSum": 0,
                "sum": 50.6
            },
            "company": {
                "scale": "6%",
                "scaleSum": 60.72,
                "fixedSum": 0,
                "sum": 60.72
            },
            "total": 111.32,
            "pro_cost": "15.00"
        },
        "失业保险": {
            "person": {
                "scale": "0",
                "scaleSum": 0,
                "fixedSum": 0,
                "sum": 0
            },
            "company": {
                "scale": "0",
                "scaleSum": 0,
                "fixedSum": 0,
                "sum": 0
            },
            "total": 0,
            "pro_cost": "15.00"
        },
        "生育保险": {
            "person": {
                "scale": "5%",
                "scaleSum": 50.6,
                "fixedSum": 0,
                "sum": 50.6
            },
            "company": {
                "scale": "6%",
                "scaleSum": 60.72,
                "fixedSum": 0,
                "sum": 60.72
            },
            "total": 111.32,
            "pro_cost": "15.00"
        },
        "工伤保险": {
            "person": {
                "scale": "5%",
                "scaleSum": 50.6,
                "fixedSum": 20,
                "sum": 70.6
            },
            "company": {
                "scale": "6%",
                "scaleSum": 60.72,
                "fixedSum": 0,
                "sum": 60.72
            },
            "total": 131.32,
            "pro_cost": "15.00"
        },
        "重大疾病医疗": {
            "person": {
                "scale": "5%",
                "scaleSum": 50.6,
                "fixedSum": 0,
                "sum": 50.6
            },
            "company": {
                "scale": "6%",
                "scaleSum": 60.72,
                "fixedSum": 0,
                "sum": 60.72
            },
            "total": 111.32,
            "pro_cost": "15.00"
        }
    }
})


Mock.mock('/Order-socialSecurity3', {
	"status":0,
	"msg":"操作成功",
	"data":{
		"person": '@integer(50,100)',
		"company": '@integer(50,100)'
	}
})