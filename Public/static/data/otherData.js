if(typeof(datajson)=='undefined'){datajson = {};}
/**
 * 性别
 * 2013-2-18
 * */
datajson.sex=[
	{"id":1,"name":"男","en":"Male"},
	{"id":2,"name":"女","en":"Female"}
];
/**
 * 证件类型
 * 2013-2-18
 * */
datajson.creType=[
	{"id":1,"name":"身份证","en":"ID Card"},
	{"id":2,"name":"军人证","en":"Officer Card"},
	{"id":3,"name":"驾驶证","en":"Drive Card"},
	{"id":4,"name":"护照","en":"Passport"},
	{"id":5,"name":"其它","en":"Other"}
];
/**
 * 工作状态
 * 2013-2-18
 * */	
datajson.jobStatus=[
	{"id":0,"name":"目前正在找工作","en":"I'm looking for jobs now"},
	{"id":1,"name":"半年内无换工作的计划","en":"I don't want to change job in 6 months"},
	{"id":2,"name":"一年内无换工作的计划","en":"I don't want to change job in one year"},
	{"id":3,"name":"观望有好的机会再考虑","en":"Only good opportunities I will consider"},
	{"id":4,"name":"我暂时不想找工作","en":"I don't want to look for jobs for the moment"}
];
/**
 * 婚姻状况
 * 2013-2-18
 * */	
datajson.marriageStatus=[
	{"id":0,"name":"未婚","en":"Unmarried"},
	{"id":1,"name":"已婚","en":"Married"},
	{"id":2,"name":"保密","en":"Secret"}
];
/**
 * 目前月薪
 * 2013-2-18
 * */
datajson.currPay=[
	{"id":1,"name":"1500以下","en":"<1500"},
	{"id":2,"name":"1500-1999","en":"1500-1999"},
	{"id":3,"name":"2000-2999","en":"2000-2999"},
	{"id":4,"name":"3000-4499","en":"3000-4499"},
	{"id":5,"name":"4500-5999","en":"4500-5999"},
	{"id":6,"name":"6000-7999","en":"6000-7999"},
	{"id":7,"name":"8000-9999","en":"8000-9999"},
	{"id":8,"name":"10000-14999","en":"10000-14999"},
	{"id":9,"name":"15000-19999","en":"15000-19999"},
	{"id":10,"name":"20000-29999","en":"20000-29999"},
	{"id":11,"name":"30000-49999","en":"30000-49999"},
	{"id":12,"name":"50000及以上","en":">=50000"}
];
/**
 * 期望薪资
 * 2013-6-7
 * */
datajson.salary=[
	{"id":0,"name":"面议","en":"Negotiable"},
	{"id":1,"name":"1500以下","en":"<1500"},
	{"id":2,"name":"1500-1999","en":"1500-1999"},
	{"id":3,"name":"2000-2999","en":"2000-2999"},
	{"id":4,"name":"3000-4499","en":"3000-4499"},
	{"id":5,"name":"4500-5999","en":"4500-5999"},
	{"id":6,"name":"6000-7999","en":"6000-7999"},
	{"id":7,"name":"8000-9999","en":"8000-9999"},
	{"id":8,"name":"10000-14999","en":"10000-14999"},
	{"id":9,"name":"15000-19999","en":"15000-19999"},
	{"id":10,"name":"20000-29999","en":"20000-29999"},
	{"id":11,"name":"30000-49999","en":"30000-49999"},
	{"id":12,"name":"50000及以上","en":">=50000"}
];

/**
 * 企业薪资待遇
 * 2013-2-18
 * */
datajson.salaryCom=[
	{"id":1,"name":"1500以下","en":"<1500"},
	{"id":2,"name":"1500-1999","en":"1500-1999"},
	{"id":3,"name":"2000-2999","en":"2000-2999"},
	{"id":4,"name":"3000-4499","en":"3000-4499"},
	{"id":5,"name":"4500-5999","en":"4500-5999"},
	{"id":6,"name":"6000-7999","en":"6000-7999"},
	{"id":7,"name":"8000-9999","en":"8000-9999"},
	{"id":8,"name":"10000-14999","en":"10000-14999"},
	{"id":9,"name":"15000-19999","en":"15000-19999"},
	{"id":10,"name":"20000-29999","en":"20000-29999"},
	{"id":11,"name":"30000-49999","en":"30000-49999"},
	{"id":12,"name":"50000及以上","en":">=50000"},
	{"id":0,"name":"暂不显示薪资","en":"Negotiable"}
];	
	
/**
 * 待遇
 * 2013-2-18
 * */
datajson.payOther=[
	{"name":"包食宿","en":"Meals and Apartment needed"},{"name":"提供住宿","en":"Apartment needed"},{"name":"提供社保","en":"Social Insurance"},{"name":"双休","en":"Two-day Weekend"},{"name":"提供五险一金","en":"Five social insurance and one housing fund"}
];	
/**
 * 婚姻状况
 * 2013-2-18
 * */	
datajson.politics=[
	{"id":1,"name":"中共党员","en":"Party Member"},
	{"id":2,"name":"团员","en":"League Member"},
	{"id":3,"name":"民主党派","en":"Democratic Party"},
	{"id":4,"name":"无党派人士","en":"No Party"},
	{"id":5,"name":"群众","en":"Citizen"},
	{"id":6,"name":"其他","en":"Others"}
];
/**
 * 注册资金
 * 2013-3-7
 * */	
datajson.registerFund=[
	{"id":0,"name":"50万以下","en":"Less than 500,000"},
	{"id":1,"name":"50万-100万","en":"500,000-1,000,000"},
	{"id":2,"name":"100万-500万","en":"1,000,000-5,000,000"},
	{"id":3,"name":"500万-1000万","en":"5,000,000-10,000,000"},
	{"id":4,"name":"1000万-5000万","en":"10,000,000-50,000,000"},
	{"id":5,"name":"5000万以上","en":"50,000,000 or more"}
];
/**
 * 工作类型
 * 2013-2-18
 * */
datajson.jobType=[
    {"id":-1,"name":"不限","en":"ALL"},
    {"id":1,"name":"全职","en":"Full-time"},
    {"id":2,"name":"兼职","en":"Part-time"},
    {"id":3,"name":"实习","en":"Trainee"},
    {"id":4,"name":"全职/兼职","en":"Full/Part-time"},
    {"id":5,"name":"兼职/实习","en":"Part/Trainee"},
    {"id":6,"name":"全职/实习","en":"Full/Trainee"}
];
datajson.jobType2=[
    {"id":1,"name":"全职","en":"Full-time"},
    {"id":2,"name":"兼职","en":"Part-time"},
    {"id":3,"name":"实习","en":"Trainee"}
];

/**
 * 到岗时间
 * 2013-2-18
 * */	
datajson.workTime=[
	{"id":1,"name":"随时到岗","en":"Immediately"},
	{"id":2,"name":"3天内","en":"Within 3 days"},
	{"id":3,"name":"1周内","en":"Within 1 week"},
	{"id":7,"name":"两周(半个月内)","en":"Within 1 month"},
	{"id":4,"name":"1个月内","en":"Within 1 month"},
	{"id":5,"name":"1—3个月","en":"From 1 to 3 months"},
	{"id":6,"name":"半年内","en":"Within 6 months"}
];

datajson.languageList=[
    {"id":1000,"name":"英语","en":"English"},
    {"id":2100,"name":"粤语","en":"Cantonese"},
    {"id":1100,"name":"日语","en":"Japanese"},
    {"id":1200,"name":"法语","en":"French"},
    {"id":1300,"name":"德语","en":"German"},
    {"id":1400,"name":"俄语","en":"Russian"},
    {"id":1500,"name":"韩语","en":"Korean"},
    {"id":1700,"name":"葡萄牙语","en":"Portuguese"},
    {"id":1600,"name":"西班牙语","en":"Spanish"},
    {"id":1900,"name":"意大利语","en":"Italian"},
    {"id":1800,"name":"阿拉伯语","en":"Arabic"},
    {"id":2000,"name":"普通话","en":"Chinese Mandarin"},
    {"id":2300,"name":"闽南话","en":"Minnan Dialect"},
    {"id":2200,"name":"上海话","en":"Shanghai Dialect"},
    {"id":2400,"name":"其他","en":"Others"}
];

datajson.language=[
	{"id":1,"name":"熟练","en":"skilled"},
	{"id":2,"name":"精通","en":"Excellent"},
	{"id":3,"name":"良好","en":"Good"},
	{"id":4,"name":"一般","en":"General"},
	{"id":1000,"name":"英语","en":"English"},
	{"id":1001,"name":"大学英语等级考试CET-4","en":"CET-4"},
	{"id":1002,"name":"大学英语等级考试CET-6","en":"CET-6"},
	{"id":1003,"name":"英语专业4级","en":"TEM-4"},
	{"id":1004,"name":"英语专业8级","en":"TEM-8"},
	{"id":1005,"name":"全国英语等级考试PETS-1初始级","en":"PETS-1"},
	{"id":1006,"name":"全国英语等级考试PETS-2中下级","en":"PETS-2"},
	{"id":1007,"name":"全国英语等级考试PETS-3中间级","en":"PETS-3"},
	{"id":1008,"name":"全国英语等级考试PETS-4中上级","en":"PETS-4"},
	{"id":1009,"name":"全国英语等级考试PETS-5最高级","en":"PETS-5"},
	{"id":1010,"name":"托福","en":"TOEFL"},
	{"id":1011,"name":"GRE","en":"GRE"},
	{"id":1012,"name":"GMAT","en":"GMAT"},
	{"id":1013,"name":"雅思","en":"IELTS"},
	{"id":1014,"name":"剑桥商务英语证书1级","en":"BEC-1"},
	{"id":1015,"name":"剑桥商务英语证书2级","en":"BEC-2"},
	{"id":1016,"name":"剑桥商务英语证书3级","en":"BEC-3"},
	{"id":1017,"name":"剑桥英语入门考试","en":"KET"},
	{"id":1018,"name":"剑桥初级英语考试","en":"PET"},
	{"id":1019,"name":"剑桥第一英语证书考试","en":"FCE"},
	{"id":1020,"name":"中级口译证书","en":"Certification of Intermediate Interpreter"},
	{"id":1021,"name":"高级口译证书","en":"Certification of Advanced Interpreter"},
	
	{"id":1100,"name":"日语","en":"Japanese"},
	{"id":1101,"name":"日语一级证书","en":"Japanese Test Band 1"},
	{"id":1102,"name":"日语二级证书","en":"Japanese Test Band 2"},
	{"id":1103,"name":"日语三级证书","en":"Japanese Test Band 3"},
	{"id":1104,"name":"日语四级证书","en":"Japanese Test Band 4"},
	
	{"id":1200,"name":"法语","en":"French"},
	{"id":1201,"name":"法语四级证书","en":"CFT4"},
	{"id":1202,"name":"法语六级证书","en":"CFT6"},
	
	{"id":1300,"name":"德语","en":"German"},
	{"id":1301,"name":"德语四级证书","en":"CGT4"},
	{"id":1302,"name":"德语六级证书","en":"CGT6"}, 
	
	{"id":1400,"name":"俄语","en":"Russian"},
	{"id":1401,"name":"俄语四级证书","en":"CRT4"},
	{"id":1402,"name":"俄语六级证书","en":"CRT6"},
	
	{"id":1500,"name":"韩语","en":"Korean"},
	{"id":1600,"name":"西班牙语","en":"Spanish"},
	{"id":1700,"name":"葡萄牙语","en":"Portuguese"},
	{"id":1800,"name":"阿拉伯语","en":"Arabic"},
	{"id":1900,"name":"意大利语","en":"Italian"},
	{"id":2000,"name":"普通话","en":"Chinese Mandarin"},
	{"id":2100,"name":"粤语","en":"Cantonese"},
	{"id":2200,"name":"上海话","en":"Shanghai Dialect"},
	{"id":2300,"name":"闽南话","en":"Minnan Dialect"},
	{"id":2400,"name":"其他","en":"Others"}
];
/**
 * 民族
 * 2013-2-21
 * */	
datajson.nation=[
	{"id":1,"name":"汉","en":"Han"},
	{"id":2,"name":"壮","en":"Zhuang"},
	{"id":3,"name":"满","en":"Manchu"},
	{"id":4,"name":"回","en":"Hui"},
	{"id":5,"name":"苗","en":"Miao"},
	{"id":6,"name":"维吾尔","en":"Uighur"},
	{"id":7,"name":"彝","en":"Yi"},
	{"id":8,"name":"土家","en":"Tujia"},
	{"id":9,"name":"蒙古","en":"Mongol"},
	{"id":10,"name":"藏","en":"Tibetan"},
	{"id":11,"name":"布依","en":"Buyi"},
	{"id":12,"name":"侗","en":"Dong"},
	{"id":13,"name":"瑶","en":"Yao"},
	{"id":14,"name":"朝鲜","en":"Korean"},
	{"id":15,"name":"白","en":"Bai"},
	{"id":16,"name":"哈尼","en":"Hani"},
	{"id":17,"name":"黎","en":"Li"},
	{"id":18,"name":"哈萨克","en":"Kazakh"},
	{"id":19,"name":"傣","en":"Dai"},
	{"id":20,"name":"畲","en":"She"},
	{"id":21,"name":"僳僳","en":"Lisu"},
	{"id":22,"name":"仡佬","en":"Gelao"},
	{"id":23,"name":"拉祜","en":"Lahu"},
	{"id":24,"name":"东乡","en":"Dongxiang"},
	{"id":25,"name":"佤","en":"Wa"},
	{"id":26,"name":"水","en":"shui"},
	{"id":27,"name":"纳西","en":"Naxi"},
	{"id":28,"name":"羌","en":"Qiang"},
	{"id":29,"name":"土","en":"Du"},
	{"id":30,"name":"锡伯","en":"Xibe"},
	{"id":31,"name":"仫佬","en":"Mulam"},
	{"id":32,"name":"柯尔克孜","en":"Kirghiz"},
	{"id":33,"name":"达斡尔","en":"Daur"},
	{"id":34,"name":"景颇","en":"Jingpo"},
	{"id":35,"name":"撒拉","en":"Salar"},
	{"id":36,"name":"布朗","en":"Blang"},
	{"id":37,"name":"毛南","en":"Maonan"},
	{"id":38,"name":"塔吉克","en":"Tajik"},
	{"id":39,"name":"普米","en":"Pumi"},
	{"id":40,"name":"阿昌","en":"Achang"},
	{"id":41,"name":"怒","en":"Nu"},
	{"id":42,"name":"鄂温克","en":"Evenki"},
	{"id":43,"name":"京","en":"Gin"},
	{"id":44,"name":"基诺","en":"Jino"},
	{"id":45,"name":"德昂","en":"De'ang"},
	{"id":46,"name":"乌孜别克","en":"Uzbek"},
	{"id":47,"name":"俄罗斯","en":"Russian"},
	{"id":48,"name":"裕固","en":"Yugur"},
	{"id":49,"name":"保安","en":"Bonan"},
	{"id":50,"name":"门巴","en":"Menba"},
	{"id":51,"name":"鄂伦春","en":"Oroqin"},
	{"id":52,"name":"独龙","en":"Drung"},
	{"id":53,"name":"塔塔尔","en":"Tatar"},
	{"id":54,"name":"赫哲","en":"Hezhen"},
	{"id":55,"name":"高山","en":"Gaoshan"},
	{"id":56,"name":"珞巴","en":"Lhoba"}
];			
/**
 * 发布时间JSON数据
 * 2012-9-26
 * */
datajson.timeLevel=[
	{id:"",name:"所有"},
	{"id":1,"name":"一天"},
	{"id":2,"name":"二天"},
	{"id":7,"name":"一周内"},
	{"id":14,"name":"两周内"},
	{"id":30,"name":"一个月内"},
	{"id":90,"name":"三个月内"},
	{"id":180,"name":"半年内"},
	{"id":365,"name":"一年内"}
];
/**
 * 学历要求JSON数据 (个人)
 * 2015-5-27
 * */
datajson.degree=[
    {"id":1,"name":"初中及以下","en":"Junior High"},
    {"id":2,"name":"高中","en":"Senior High"},
    {"id":3,"name":"中专","en":"Technical School"},
    {"id":4,"name":"大专","en":"College"},
    {"id":5,"name":"本科","en":"Bachelor"},
    {"id":6,"name":"硕士","en":"Master"},
    {"id":8,"name":"博士","en":"Doctor"}
];
/**
 * 学历要求JSON数据（职位）
 * 2015-5-27
 * */
datajson.degree2=[
    {"id":1,"name":"初中及以下","en":"Junior High"},
    {"id":2,"name":"高中","en":"Senior High"},
    {"id":3,"name":"中专","en":"Technical School"},
    {"id":4,"name":"大专","en":"College"},
    {"id":5,"name":"本科","en":"Bachelor"},
    {"id":6,"name":"硕士及以上","en":"Master"}
];

/**
 * 工作年限JSON数据
 * 2012-9-26
 * */	
datajson.workyear=[
	{"id":-1,"name":"在读学生","en":"Student"},
	{"id":0,"name":"应届毕业生","en":"Graduates"},
	{"id":1,"name":"1年","en":"1 year"},
	{"id":2,"name":"2年","en":"2 years"},
	{"id":3,"name":"3年","en":"3 years"},
	{"id":4,"name":"4年","en":"4 years"},
	{"id":5,"name":"5年","en":"5 years"},
	{"id":6,"name":"6年","en":"6 years"},
	{"id":7,"name":"7年","en":"7 years"},
	{"id":8,"name":"8年","en":"8 years"},
	{"id":9,"name":"9年","en":"9 years"},
	{"id":10,"name":"10年","en":"10 years"},
	{"id":11,"name":"10年以上","en":">10 years"}
];
/**
 * 单位性质JSON数据
 * 2012-9-26
 * */	
datajson.comType=[
	{"id":1,"name":"外资企业","en":"Foreign Enterprise"},
	{"id":2,"name":"中外合营（合资、合作）","en":"Joint Venture"},
	{"id":3,"name":"台资企业","en":"Taiwan-funded Enterprise"},
	{"id":4,"name":"港资企业","en":"Hong Kong-funded Enterprise"},
	{"id":5,"name":"私营·民营企业","en":"Private Enterprise·Civil Enterprise"},
	{"id":6,"name":"股份制企业","en":"Stock company"},
	{"id":7,"name":"跨国公司（集团）","en":"Multinational Enterprise"},
	{"id":8,"name":"国有企业","en":"State Owned Enterprise"},
	{"id":9,"name":"事业单位","en":"Career Office"},
	{"id":10,"name":"社会团体","en":"Caste"},
	{"id":11,"name":"政府机关","en":"Government"},
	{"id":20,"name":"其他","en":"Others"}
];	
/**
 * 单位规模JSON数据
 * 2012-9-26
 * */	
datajson.comScale=[
	{"id":1,"name":"1-100","en":"1-100"},
	{"id":2,"name":"100-200","en":"100-200"},
	{"id":3,"name":"200-500","en":"200-500"},
	{"id":4,"name":"500-1000","en":"500-1000"},
	{"id":5,"name":"1000-2000","en":"1000-2000"},
	{"id":6,"name":"2000以上","en":"2000 or more"}
];	

/**
 * 行业JSON数据
 * 2012-9-26
 * */
datajson.industry=[
    {"id":1,"name":"互联网/电子商务","en":"Internet/E-Commerce"},
    {"id":2,"name":"计算机软件","en":"Computers Software"},
    {"id":3,"name":"计算机硬件","en":"Computers Hardware"},
    {"id":4,"name":"电子/微电子技术/集成电路","en":"Electrical/Micro-electronics/Integrated circuit"},
    {"id":5,"name":"通讯/电信业","en":"Telecommunications"},
    {"id":6,"name":"快速消费品","en":"Fast consumable"},
    {"id":7,"name":"服装/纺织/皮革","en":"Dress/Textile/leather"},
    {"id":8,"name":"金融业（银行、保险、证券、投资、基金）","en":"Finance(Banking、Venture Capital、Insurance)"},
    {"id":9,"name":"家具/家电/玩具/礼品","en":"Furniture/Household appliances/Toy/Gift"},
    {"id":10,"name":"贸易/商务/进出口","en":"Tading/Commerce/Imports and Exports"},
    {"id":11,"name":"生产/制造/加工","en":"Manufacturing"},
    {"id":12,"name":"房地产/建筑/建材/工程","en":"Real estate/Build/Project"},
    {"id":13,"name":"钢铁/机械/设备/重工","en":"Iron/Machine-building/Machine/Heavy industry"},
    {"id":14,"name":"交通/运输/物流•快递","en":"Transportation/Distribution"},
    {"id":15,"name":"广告/创意/设计","en":"Advertising•create/Design"},
    {"id":16,"name":"批发/零售（超市、百货、商场、专卖店）","en":"Sales"},
    {"id":17,"name":"汽车/摩托车及零配件","en":"Automobile/Autocycle installation"},
    {"id":18,"name":"仪器仪表/电工设备/工业自动化","en":"Apparatus/Electric devices/Industrialc"},
    {"id":19,"name":"医药/生物工程","en":"Medicine/Bioengineering"},
    {"id":20,"name":"餐饮/酒店/旅游","en":"Tourism/Catering/Entertainment/Hotels"},
    {"id":21,"name":"橡胶/塑胶/五金","en":"Rubber/Plastic/Hardware"},
    {"id":22,"name":"印刷/包装/造纸","en":"Press/Casing/Paper making"},
    {"id":23,"name":"电力/电气/水利","en":"Electric Power/Electrical/Water conservancy"},
    {"id":24,"name":"石油/化工/地质","en":"Petroleum/Chemical engineering/ geology"},
    {"id":25,"name":"办公设备/文体休闲用品/家居用品","en":"OA devices/Culture Articles/Home articles"},
    {"id":26,"name":"法律/法务","en":"Law"},
    {"id":27,"name":"媒体/出版/影视/文化传播","en":"Media/Publishin/Movie facture/Culture transmit"},
    {"id":28,"name":"艺术/文体","en":"Arts/recreation and sports"},
    {"id":29,"name":"娱乐/体育/休闲","en":"Entertainment/Sports/Leisure"},
    {"id":30,"name":"教育/培训/科研院所","en":"Education/Training/Research"},
    {"id":31,"name":"咨询与调查业（顾问/企业管理/知识产权）","en":"Consultation(Consultant/Business management/ Intellectual property rights)"},
    {"id":32,"name":"医疗/护理/美容/保健/卫生服务","en":"Medical Treatment/nurse/Cosmetology Health/Sanitation"},
    {"id":33,"name":"人才交流/中介服务","en":"Human Resources"},
    {"id":34,"name":"政府/公用事业/社区服务","en":"Government/Public service/Community service "},
    {"id":35,"name":"农、林、牧、副、渔业","en":"Agriculture/Forestry/Animal husbandry/Fishery"},
    {"id":36,"name":"协会/社团/非营利机构","en":"Community/Social services/Government/Nonprofit"},
    {"id":38,"name":"IT服务（系统/数据/维护）","en":"IT service(system, data, Maintain)"},
    {"id":39,"name":"网络游戏","en":"Online games"},
    {"id":40,"name":"珠宝/首饰/钟表","en":"Jewels/ewelry/Clocks"},
    {"id":41,"name":"会计/审计","en":"Accountant/Audit"},
    {"id":42,"name":"信托/担保/拍卖/典当","en":"Trust/Assure/Auction/Pawn"},
    {"id":43,"name":"奢侈品/收藏品/工艺品","en":"Luxury/Collection/Craftware  "},
    {"id":44,"name":"物业管理/商业中心","en":"Property management/Commercial centre"},
    {"id":45,"name":"外包服务","en":"Outsourcing service"},
    {"id":46,"name":"人力资源服务","en":"Human resource service"},
    {"id":47,"name":"检测/认证","en":"Testing/Authentication"},
    {"id":48,"name":"租赁服务","en":"Rental service"},
    //{"id":49,"name":"医药/生物工程","en":"Medicine/Bioengineering"},
    {"id":50,"name":"环保","en":"Environmental protection"},
    {"id":51,"name":"航天/航空","en":"Space flight/Aviation"},
    {"id":52,"name":"多元化业务集团","en":"Diversified business group"},
    {"id":53,"name":"家居/室内设计/装潢","en":"Home Furnishing/Interior design/Decoration"},
    {"id":54,"name":"公关/市场推广/会展","en":"Public relations/Market promotion/Exhibition"},
    {"id":55,"name":"能源/矿产/采掘/冶炼","en":"Energy/Mineral products/Mining/Smelting"},
    {"id":37,"name":"其他","en":"Other"}
];
 
/**
 * 淘职标签JSON数据
 * 2012-9-26
 * */
datajson.tagJobs=[
	{"id":1100,"name":"食宿标签","value":0},
	{"id":1101,"name":"出差餐补","value":19},
	{"id":1102,"name":"包食宿","value":20},
	{"id":1103,"name":"住房补贴","value":21},
	{"id":1104,"name":"住房公积金","value":22},
	{"id":1105,"name":"有食堂","value":23},
	{"id":1106,"name":"提供宿舍","value":4},		
	{"id":1200,"name":"工时标签","value":0},
	{"id":1201,"name":"八小时工作制","value":24},
	{"id":1202,"name":"弹性工作时间","value":25},
	{"id":1203,"name":"周末双休","value":26},
	{"id":1204,"name":"朝九晚五","value":12},
	{"id":1205,"name":"带薪年假","value":8},
	{"id":1206,"name":"10天以上年假","value":27},
	{"id":1300,"name":"办公环境","value":0},
	{"id":1301,"name":"办公环境优美","value":28},
	{"id":1302,"name":"甲A写字楼","value":29},
	{"id":1303,"name":"独立工作间","value":30},
	{"id":1304,"name":"独立办公室","value":31},
	{"id":1305,"name":"笔记本电脑","value":32},
	{"id":1400,"name":"交通标签","value":0},
	{"id":1401,"name":"交通便利","value":33},
	{"id":1402,"name":"交通补贴","value":34},
	{"id":1403,"name":"地铁口","value":35},
	{"id":1404,"name":"班车接送","value":36},
	{"id":1500,"name":"定位标签","value":0},
	{"id":1501,"name":"管理","value":37},
	{"id":1502,"name":"世界500强","value":38},
	{"id":1503,"name":"中国500强","value":39},
	{"id":1504,"name":"高收入","value":40},
	{"id":1505,"name":"高提成","value":41},
	{"id":1506,"name":"行业领先","value":42},
	{"id":1600,"name":"发展标签","value":0},
	{"id":1601,"name":"免费培训","value":43},
	{"id":1602,"name":"储备干部","value":44},
	{"id":1603,"name":"定期户外活动","value":45},
	{"id":1604,"name":"良性竞争机制","value":46},
	{"id":1605,"name":"定期培训","value":47},
	{"id":1606,"name":"岗位竞聘","value":48},
	{"id":1607,"name":"有乒乓球桌","value":49},
	{"id":1608,"name":"免费图书馆","value":50},
	{"id":1609,"name":"企业商学院","value":51},
	{"id":1700,"name":"其它福利","value":0},
	{"id":1701,"name":"带薪旅游","value":52},
	{"id":1702,"name":"年终分红","value":53},
	{"id":1703,"name":"季度奖金","value":54},
	{"id":1704,"name":"生日祝福及礼物","value":55},
	{"id":1705,"name":"年终抽奖","value":56},
	{"id":1706,"name":"节假日发放礼品","value":57}
 ];
/**
 * 证书JSON数据
 * 2012-9-26
 **/
 datajson.cert=[
	{"id":1100,"name":"外语证书","en":"Foreign Language Certificate"}, 
	{"id":1101,"name":"大学英语四级","en":"CET4"},
	{"id":1102,"name":"大学英语六级","en":"CET6"},
	{"id":1103,"name":"英语专业四级","en":"TEM Level 4"},
	{"id":1104,"name":"英语专业八级","en":"TEM Level 8"},
	{"id":1105,"name":"托福","en":"TOEFL"},
	{"id":1106,"name":"托业","en":"TOEIC"},
	{"id":1107,"name":"GRE","en":"GRE"},
	{"id":1108,"name":"GMAT","en":"GMAT"},
	{"id":1109,"name":"雅思","en":"IELTS"},
	{"id":1110,"name":"剑桥商务英语证书1级BEC1","en":"BEC1"},
	{"id":1111,"name":"剑桥商务英语证书2级BEC2","en":"BEC2"},
	{"id":1112,"name":"剑桥商务英语证书3级BEC3","en":"BEC3"},
	{"id":1113,"name":"剑桥英语入门考试 KET","en":"KET"},
	{"id":1114,"name":"剑桥初级英语考试 PET","en":"PET"},
	{"id":1115,"name":"剑桥第一英语证书考试 FCE","en":"FCE"},
	{"id":1116,"name":"全国公共英语等级考试 PETS","en":"PETS"},
	{"id":1117,"name":"通用英语初级","en":"General English Certificate (Beginners)"},
	{"id":1118,"name":"通用英语中级","en":"General English Certificate (Intermediate)"},
	{"id":1119,"name":"中级口译证书","en":"Certification of intermediate interpreter"},
	{"id":1120,"name":"高级口译证书","en":"Certification of advanced interpreter"},
	{"id":1121,"name":"俄语四级证书","en":"CRT4"},
	{"id":1122,"name":"俄语六级证书","en":"CRT6"},
	{"id":1123,"name":"法语四级证书","en":"CFT4"},
	{"id":1124,"name":"法语六级证书","en":"CFT6"},
	{"id":1125,"name":"日语一级证书","en":"Japanese Test Band1"},
	{"id":1126,"name":"日语二级证书","en":"Japanese Test Band2"},
	{"id":1127,"name":"日语三级证书","en":"Japanese Test Band3"},
	{"id":1128,"name":"日语四级证书","en":"Japanese Test Band4"},
	{"id":1129,"name":"实用日本语鉴定证书（J.TEST）","en":"J.TEST"},
	{"id":1130,"name":"德语四级证书","en":"CGT4"},
	{"id":1131,"name":"德语六级证书","en":"CGT6"},
                                                     
	{"id":1200,"name":"计算机证书","en":"Computer Certificate"},
	{"id":1201,"name":"全国计算机等级考试一级","en":"Nationwide Computer Level Test Band 1"},            
	{"id":1202,"name":"全国计算机等级考试二级","en":"Nationwide Computer Level Test Band 2"},            
	{"id":1203,"name":"全国计算机等级考试三级","en":"Nationwide Computer Level Test Band 3"},            
	{"id":1204,"name":"全国计算机等级考试四级","en":"Nationwide Computer Level Test Band 4"},            
	{"id":1205,"name":"全国计算机应用技术证书（NIT）","en":"NIT"},                                              
	{"id":1206,"name":"计算机软件专业技术资格和水平考试","en":"Nationwide Software Technology Test"},              
	{"id":1207,"name":"初级：程序员","en":"Certified Primary Programmer"},            
	{"id":1208,"name":"网络管理员","en":"Network Administrator"},     
	{"id":1209,"name":"中级：软件设计师","en":"Intermediate: Software Designer"},         
	{"id":1210,"name":"网络工程师","en":"Network Engineer"},                  
	{"id":1211,"name":"软件评测师","en":"Software Testing Engineer"},          
	{"id":1212,"name":"多媒体应用设计师","en":"Multimedia Appliance Designer"},             
	{"id":1213,"name":"信息系统监理师","en":"Information System Supervisor"},             
	{"id":1214,"name":"高级：系统分析师","en":"High-level: System Analyst"},            
	{"id":1215,"name":"信息系统项目管理师","en":"Information System Project Management Engineer"},
	{"id":1216,"name":"微软认证产品专家{MCP}","en":"MCP"},                                         
	{"id":1217,"name":"微软认证系统工程师{MCSE}","en":"MCSE"},                                          
	{"id":1218,"name":"微软认证数据库管理员{MCDBA}","en":"MCDBA"},                                        
	{"id":1219,"name":"微软认证软件开发专家（MCSD）","en":"MCSD"},                                        
	{"id":1220,"name":"Adobe中国认证专业平面设计师","en":"Adobe Certified Graphic Designer"},               
	{"id":1221,"name":"Adobe中国认证网页设计师","en":"Adobe Certified Website Designer"},               
	{"id":1222,"name":"Adobe中国认证数码视频设计师","en":"Adobe Certified Digital Video Designer"},       
	{"id":1223,"name":"Adobe中国认证商务出版设计师","en":"Adobe Certified Business Publishing Designer"},   
	{"id":1224,"name":"Cisco职业资格认证","en":"CCNA"},                                          
	{"id":1225,"name":"Cisco职业资格认证","en":"CCNP"},                                          
	{"id":1226,"name":"Cisco职业资格认证","en":"CCIE"},                                          
	{"id":1227,"name":"Cisco职业资格认证","en":"CCDA"},                                          
	{"id":1228,"name":"Cisco职业资格认证","en":"CCDP"},                                          
	{"id":1229,"name":"Lotus-CLS资格认证","en":"Lotus-CLS Certification"},                      
	{"id":1230,"name":"Lotus-CLP资格认证","en":"Lotus-CLP Certification"},                      
	{"id":1231,"name":"Lotus-CLI资格认证","en":"Lotus-CLI Certification"},                      
	{"id":1232,"name":"Notes应用开发工程师","en":"Notes Appliancation Developing Engineer"},         
	{"id":1233,"name":"Notes系统管理工程师","en":"Notes System Administration Engineer"},            
	{"id":1234,"name":"Notes高级应用开发工程师","en":"Notes Advanced Appliancation Developing Engineer"},
	{"id":1235,"name":"Notes高级系统管理工程师","en":"Notes Advanced System Administration Engineer"},   
	{"id":1236,"name":"IBM-BD2数据库管理员","en":"IBM-BD Database Administrator"},                 
	{"id":1237,"name":"IBM-BD2应用开发专家","en":"IBM-BD Appliancation Developer"},                 
	{"id":1238,"name":"IBM-MQSeries工程师","en":"IBM Certified Specialist-MQSeries"},              
	{"id":1239,"name":"Oracle8数据库管理员","en":"Oracle Database Administrator"},                

	{"id":1300,"name":"会计证书","en":"Accountant Cerfificate"},                                     
	{"id":1301,"name":"注册会计师","en":"Certified Public Accountant"},                           
	{"id":1302,"name":"高级会计师","en":"Senior Accountant"},                                  
	{"id":1303,"name":"中级会计师","en":"Accountant"},                                            
	{"id":1304,"name":"助理会计师","en":"Assistant Accountant"},                                  
	{"id":1305,"name":"会计上岗证","en":"Certificate of Accounting Professional"},                
	{"id":1306,"name":"会计电算化证书","en":"Accounting computerized certificate"},                   
	{"id":1307,"name":"国际账务会计证书","en":"International Financing & Accounting Certificate"},
           
	{"id":1400,"name":"职称证书","en":"Administrative Level Certificate"},                           
	{"id":1401,"name":"初级工程师","en":"Junior Engineer"},                                       
	{"id":1402,"name":"中级工程师","en":"Engineer"},                                              
	{"id":1403,"name":"高级工程师","en":"Senior Engineer"},                                       
	{"id":1404,"name":"初级经济师","en":"Junior Economist"},                                      
	{"id":1405,"name":"中级经济师","en":"Economist"},                                             
	{"id":1406,"name":"高级经济师","en":"Senior Economist"},                                      
	{"id":1407,"name":"助理工程师","en":"Assistant Engineer"},                                    
	{"id":1408,"name":"助理经济师","en":"Assistant Economist"}, 
                            
	{"id":1500,"name":"其它证书","en":"Other Certificate"},                                          
	{"id":1501,"name":"全国律师资格证书","en":"Nationwide Lawyer Certificate"},
	{"id":1502,"name":"企业法律顾问执业资格证书","en":"Enterprise Legal Adviser Certificate"},
	{"id":1503,"name":"注册建筑师","en":"Registered Architect"},                                  
	{"id":1504,"name":"注册结构师","en":"Registered Constructer"},                                
	{"id":1505,"name":"注册土木工程师","en":"Registered Civil Engineer"},                             
	{"id":1506,"name":"监理工程师执业资格证书","en":"Certificate of Construction Supervisor"},
	{"id":1507,"name":"造价工程师执业资格证书","en":"Certificate of Cost Engineer"},
	{"id":1508,"name":"注册咨询工程师（投资）执业资格证书","en":"Registered Consultant (Investment)"},
	{"id":1509,"name":"房地产估价师执业资格证书","en":"Real Estate Appraiser Certificate"},
	{"id":1510,"name":"房地产经纪人执业资格证书","en":"Real Estate Agent Certificate"},
	{"id":1511,"name":"电子商务师职业资格证书","en":"E-Business Specialist Certificate"},
	{"id":1512,"name":"注册税务师","en":"Registered Tax Agent"},
	{"id":1513,"name":"注册资产评估师","en":"Certificate Public Value of China"},
	{"id":1514,"name":"统计上岗证","en":"Certificate of Statistician"},                           
	{"id":1515,"name":"执业药师资格证书","en":"Certificate of Pharmacist"},
	{"id":1516,"name":"公共关系资格证书","en":"Public Relations Certificate"},
	{"id":1517,"name":"ISO体系内审员/注册审核员","en":"ISO Internal Auditor/Registed Auditor"},
	{"id":1518,"name":"报关员资格证书","en":"Customs Declarer Certificate"},                          
	{"id":1519,"name":"报检员资格证书","en":"Entry/Exit Inspection and Quarantine Certificate"},      
	{"id":1520,"name":"外销员资格证书","en":"Foreign Business Certificate"},                          
	{"id":1521,"name":"保险代理人资格证书","en":"Certificate Insurance Agent"},
	{"id":1522,"name":"导游人员资格证书","en":"Tourist Guide Certificate"},
	{"id":1523,"name":"办公自动化证书","en":"Office Automation Certificate"},                       
	{"id":1524,"name":"珠算技术等级证书","en":"Abacus Grade Certificate"},                            
	{"id":1525,"name":"普通话等级证书","en":"Mandarin Certificate"},                                  
	{"id":1526,"name":"驾驶执照","en":"Driver’s License"}
 ];
