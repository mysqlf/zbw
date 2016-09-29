var Mock = require('mockjs');
var area = require('./area');


Mock.mock('/getArea', {
	'state': true,
    'msg': '@cname',
    'data': area
})