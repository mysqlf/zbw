var Mock = require('mockjs');

Mock.mock('/Service-Rules-sbRules', {
	'status': 0,
    'msg': '@cname',
    'data': { "items": [{ "name": "养老保险", "rules": { "company": "13%+0", "person": "8%+0", "amount": "2030", "replenish": 1 } }, { "name": "医疗保险", "rules": { "company": "6%+0", "person": "2%+0", "amount": "2030", "replenish": 0 } }, { "name": "生育保险", "rules": { "company": "0.5%+0", "person": "0%+0", "amount": "2030", "replenish": 0 } }, { "name": "工伤保险", "rules": { "company": "0.1%+0", "person": "0%+0", "amount": "2030", "replenish": 0 } }, { "name": "补充医疗保险", "rules": { "company": "0.2%+0", "person": "0%+0", "amount": "2030", "replenish": 0 } }, { "name": "失业保险", "rules": { "company": "0%+18.27", "person": "0%+10.15", "amount": "2030", "replenish": 0 } }], "min": 2030, "max": 18162, "pro_cost": 10, "material": "不需要","disabled": 10, "other":[{"name":"费用1","rules":{"company":100,"person":100}},{"name":"费用2","rules":{"company":100,"person":10}}] }

})
