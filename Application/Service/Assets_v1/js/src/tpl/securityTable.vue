<template>
    <table class="table table-bordered table-border text-center" style="width: 674px;">
        <thead>
            <tr>
                <th rowspan="2" width="16%">
                    险种
                </th>
                <th colspan="2">
                    单位缴纳
                </th>
                <th colspan="2">
                    个人缴纳
                </th>
                <th rowspan="2">合计金额</th>

            </tr>
            <tr>
                <th>比例</th>
                <th>
                    金额
                </td>
                <th>比例</th>
                <th>
                    金额
                </th>
            </tr>
        </thead>
        <tbody>

            {{each items}}
                <tr>
                    <td>
                        {{$value.name}}
                    </td>
                    <td class="sum-company-scale">
                        {{$value.company.scale}}+{{$value.company.fixedSum}}

                    </td>
                    <td class="sum-company">{{$value.company.sum}}</td>
                    <td class="sum-person-scale">
                        {{$value.person.scale}}+{{$value.person.fixedSum}}
                    </td>
                    <td class="sum-person">{{$value.person.sum}}</td>
                    <td class="sum">{{$value.total}}</td>
                </tr>
            {{/each}}
            <tr>
                <td>
                   合计
                </td>
                <td id="total-sum-company-scale">
                    /
                </td>
                <td id="total-sum-company">/</td>
                <td id="total-sum-person-scale">
                    /
                </td>
                <td id="total-sum-person">/</td>

                <td id="total">
                    /
                </td>
            </tr>
        </tbody>
    </table>
</template>