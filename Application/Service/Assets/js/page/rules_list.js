let { toggleRulesStauts } = require('api/rules');

let modules = {
    init() {
        let self = this;

        //启用和禁用规则
        $('[data-act="toggleRulesStauts"]').click(function(){
            let $this = $(this),
                { flag, state } = $this.data(); //-9 禁用 1 启用

            state -= 0;

            if( flag ) {
                return;
            }

            $this.data('flag', true);

            if (state === 1) {//目前是启用状态
                layer.confirm('是否禁用此规则？', {
                    title: '禁用规则',
                    btn: ['禁用', '取消'],
                    yes(){
                        self.toggleStauts($this);
                    },
                    end(){
                        $this.data('flag', false);
                    }
                })
            } else {
                self.toggleStauts($this);
            }

        })
    },
    // 禁用、启用规则
    toggleStauts($this){
        let { id, state } = $this.data();

        toggleRulesStauts( { id }, ( { msg = '设置成功'} ) => {

            if( state - 0 === 1 ) {
                $this.html('恢复启动')
                    .data('state', -9)
                    .closest('.list-item')
                    .addClass('disabled')
                    .find('.J_state').html('禁用');
            } else {
                $this.html('禁止启动')
                    .data('state', 1)
                    .closest('.list-item')
                    .removeClass('disabled')
                    .find('.J_state').html('启用');
            }

            layer.msg(msg)

        }, ( { msg = "设置失败" } ) => {

            layer.msg(msg)

        }).complete(() => {
            $this.data('flag', false);
        });
    }
}

module.exports = modules;

