<template>
	<form id="J_handle-layer-form">
	<input type="hidden" value="{{socPiiId}}" name="socPiiId">
	<input type="hidden" value="{{proPiiId}}" name="proPiiId">
	<input type="hidden" value="3" name="type">
	<div class="handle-layer-title">社保</div>
		<ul class="handle-layer-box">
			{{each result['1'] as item key}}
			<li class="handle-layer-list">
				<span class="dates">{{item.pay_date}}</span>

				<input type="hidden" value="{{item.pay_date}}" name="data[1][{{item.id}}][pay_date]">

				<label class="icheck-label">
					<input class="icheck J_operate-state" required {{if item.handle_result}}checked{{/if}} type="radio" name="data[1][{{item.id}}][operate_state]" value="3">
					办理成功
				</label>
				{{if item.type != 3}}
				<label class="icheck-label">
					<input class="icheck J_operate-state" {{if !item.handle_result}}checked{{/if}} required type="radio" name="data[1][{{item.id}}][operate_state]" value="-3">
					办理失败
				</label>
				{{/if}}
				<!-- <label class="icheck-label">
					<input class="icheck J_is-hang-up" type="checkbox" name="data[1][{{item.id}}][is_hang_up]" value="0">
					挂起
				</label> -->
				<dl class="horizontal horizontal-4em inline-block">
					<dt class="left text-right">备注 </dt>
					<dd class="right">
						<input class="ipt" type="text" name="data[1][{{item.id}}][remark]" value="">
					</dd>
				</dl>
			</li>
			{{/each}}
		</ul>
		{{if result['2']}}
		<div class="handle-layer-title">公积金</div>
		<ul class="handle-layer-box">
			{{each result['2'] as item key}}
			<li class="handle-layer-list">
				<span class="dates">{{item.pay_date}}</span>
				<input type="hidden" value="{{item.pay_date}}" name="data[2][{{item.id}}][pay_date]">
				<label class="icheck-label">
					<input class="icheck J_operate-state" required {{if item.handle_result}}checked{{/if}} type="radio" name="data[2][{{item.id}}][operate_state]" value="3">
					办理成功
				</label>
				{{if item.type != 3}}
				<label class="icheck-label">
					<input class="icheck J_operate-state" {{if !item.handle_result}}checked{{/if}} required type="radio" name="data[2][{{item.id}}][operate_state]" value="-3">
					办理失败
				</label>
				{{/if}}
				<!-- <label class="icheck-label">
					<input class="icheck J_is-hang-up" type="checkbox" name="data[2][{{item.id}}][is_hang_up]" value="0">
					挂起
				</label> -->
				<dl class="horizontal horizontal-4em inline-block">
					<dt class="left text-right">备注 </dt>
					<dd class="right">
						<input class="ipt" type="text" name="data[2][{{item.id}}][remark]" value="">
					</dd>
				</dl>
			</li>
			{{/each}}
		</ul>
		{{/if}}

		<div class="handle-layer-title">代办</div>

		<div class="agency-opts">
			<label class="icheck-label">
				<input class="icheck" type="checkbox" name="socBuyCard" value="1">
				代办社保卡
			</label>
			{{if result['2']}}
			<label class="icheck-label">
				<input class="icheck" type="checkbox" name="proBuyCard" value="1">
				代办公积金卡
			</label>
			{{/if}}
		</div>

	</form>
</template>

<script type="text/javascript">

	module.exports = {
		init(){
			$('.J_is-hang-up').on('ifChanged', function(){
				let $this = $(this),
					$scope = $this.closest('.handle-layer-list'),
					$state = $scope.find('.J_operate-state');

				if($this.is(':checked')) {
					$state.eq(1).iCheck('check');
				}
			})

			$('.J_operate-state').on('ifChanged', function(){
				let $this = $(this),
					$scope = $this.closest('.handle-layer-list'),
					$hangUp = $scope.find('.J_is-hang-up'),
					$state = $scope.find('.J_operate-state:checked');

				if($state.val() === '3') {
					$hangUp.iCheck('uncheck');
				}
			})
		}
	}
	
</script>
