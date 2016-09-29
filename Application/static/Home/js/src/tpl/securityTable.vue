<template>
    <table class="pageTable" width="676">
        <tr>
            <td rowspan="2" class="c-1">险种 </td>
            <td rowspan="2" class="c-1">基数 </td>
            <th colspan="2">单位缴纳</th>
            <th colspan="2">个人缴纳</th>
            <td rowspan="2" class="c-6">合计金额</td>
        </tr>
        <tr>
            <td class="c-2">比例</td>
            <td class="c-3">金额</td>
            <td class="c-4">比例</td>
            <td class="c-5">金额</td>
        </tr>
        {{each items}}
            <tr>
                <td>
                    {{$value.name}}
                </td>
                <td>
                    {{$value.amount}}
                </td>
                <td class="sum-company-scale">
                    {{$value.company.scale}}+{{$value.company.fixedSum}}

                </td>
                <td class="sum-company">{{$value.company.sum}}</td>
                <td class="sum-person-scale">
                    {{$value.person.scale}}+{{$value.person.fixedSum}}
                </td>
                <td class="sum-person">{{$value.person.sum}}</td>
                <td class="sum orange">{{$value.total}}</td>
            </tr>
        {{/each}}
        <tr>
            <td></td>
            <td></td>
            <td colspan="2">企业共缴纳:<span id="total-sum-company"></span></td>
            <td colspan="2">个人共缴纳:<span id="total-sum-person"></span></td>
            <td class="orange" id="total">/</td>
        </tr>
    </table>
</template>