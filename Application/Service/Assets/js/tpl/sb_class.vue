<template>
	{{each items as item i}}
		<dl class="horizontal horizontal-ipt horizontal-5em">
            <dt class="left">{{item.name}}：</dt>
            <dd class="right size2">
                <select class="J_sb-type" name="classifyMixed">
					{{each item._child as child}}
						<option value="{{child.id}}" {{if classify_mixed == child.id}}selected{{/if}}>{{child.name}}</option>
					{{/each}}
                </select>
            </dd>
        </dl>
	{{/each}}
	
</template>
