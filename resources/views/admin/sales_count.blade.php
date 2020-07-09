<div id="sales_count" style="height: 400px;width: 100%"></div>
<script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
<script src="/vendor/echarts/macarons.js"></script>
<script src="/vendor/echarts/china.js"></script>
<script>
    $(function () {
        $.get('/api/sales_count').done(function (data) {
            console.log(data);
            var product = [];
            var num = [];

            $.each(data, function (k, v) {
                product.push(v.name);
                num.push(v.num)
            })
            var myChart = echarts.init(document.getElementById('sales_count'), 'macarons');
            // 指定图表的配置项和数据
            myChart.setOption(
                {
                    title: {
                        text: '菜品销量统计',
                        subtext: '销量'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['销量']
                    },
                    toolbox: {
                        show: true,
                        feature: {
                            dataView: {show: true, readOnly: false},
                            magicType: {show: true, type: ['line', 'bar']},
                            restore: {show: true},
                            saveAsImage: {show: true}
                        }
                    },
                    calculable: true,
                    xAxis: [
                        {
                            type: 'category',
                            data: product
                        }
                    ],
                    yAxis: [
                        {
                            type: 'value'
                        }
                    ],
                    series: [
                        {
                            name: '销量',
                            type: 'bar',
                            data: num,
                            markPoint: {
                                data: [
                                    {type: 'max', name: '最大值'},
                                    {type: 'min', name: '最小值'}
                                ]
                            },
                            markLine: {
                                data: [
                                    {type: 'average', name: '平均值'}
                                ]
                            }
                        }
                    ]
                }
            );
        });
    });
</script>