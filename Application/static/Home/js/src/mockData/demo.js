var Mock = require('mockjs');

Mock.mock('/list/mylist', {
	'title': '@cword(3, 5)',
    'list|1-20': ['@cname']
})