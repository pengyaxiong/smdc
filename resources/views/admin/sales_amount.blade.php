<div id="sales_amount" style="height: 400px;width: 100%"></div>
<script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
<script src="/vendor/echarts/macarons.js"></script>
<script src="/vendor/echarts/china.js"></script>
<script>
    $(function () {
        $.get('/api/sales_amount').done(function (data) {
            console.log(data);
            var desk = [];      //餐桌

            $.each(data.amount, function (k, v) {
                desk.push(k);
            })
            console.log(desk);
            var myChart = echarts.init(document.getElementById('sales_amount'), 'macarons');
            // 指定图表的配置项和数据
            myChart.setOption({
                title: {
                    text: '本周销售额',
                    subtext: data.week_start + ' ~ ' + data.week_end
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: desk
                },
                toolbox: {
                    show: true,
                    feature: {
                        dataZoom: {},
                        dataView: {readOnly: false},
                        magicType: {type: ['line', 'bar']},
                        restore: {},
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        formatter: '{value}'
                    }
                },
                series: data.series_data
            });
        });
    });
</script>