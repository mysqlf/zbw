let { msgDetail } = require('api/Insurance');
let msgList = {
    init() {
        let self = this;
        self.msgDetail();
    },
    msgDetail() {
        $('.msgBtn').click(function() {
            let $this = $(this),
                { id } = $this.data(),
                $scope = $this.closest('tbody'),
                $tree = $this.parent();
            $scope.find('.detail_con').stop().animate({ 'height': '30px' });
            if ($tree.css('height') == '30px') {
                $scope.find('.detail_box').html('').css({ 'height': '0px' });
                msgDetail({ id: id }, (data) => {

                    let result = data.result,
                        html = `<div class="de_tit">${result.title}</div><div class="de_con">${result.detail}</div>`;
                    $tree.find('.detail_box').html(html).css({ 'height': '120px' });
                    $tree.stop().animate({ 'height': '150px' });
                    $tree.parent().removeClass('un_read');

                })
            } else {
                $tree.stop().animate({ 'height': '30px' });
                $tree.find('.detail_box').html('').css({ 'height': '0px' });
            }
        });
    }
}
module.exports = msgList
