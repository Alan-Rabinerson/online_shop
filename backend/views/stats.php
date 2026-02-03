<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';


$sql = "SELECT * FROM `024_total_income_per_product`";
$result = mysqli_query($conn, $sql);
$stats = mysqli_fetch_all($result, MYSQLI_ASSOC);
$chartData = [['Product Name', 'Total Income']];
foreach ($stats as $stat) {
    $chartData[] = [$stat['product_name'], (float)$stat['total_income']];
}
$chartData= json_encode($chartData);
// compute layout helpers to ensure long product names are visible
$stats_count = count($stats);
$maxLabelLen = 0;
foreach ($stats as $s) {
    $len = mb_strlen($s['product_name']);
    if ($len > $maxLabelLen) $maxLabelLen = $len;
}
// left margin in pixels: scale with longest label but clamp to reasonable bounds
$leftMargin = max(200, min(700, (int)($maxLabelLen * 7)));
// approximate chart height based on number of rows
$chartHeight = max(300, $stats_count * 40 + 100);
?>
<h2 class="mt-4">Stats</h2>
<h3 class="mb-4">Total Income Per Product</h3>
<div id="total-income-per-product" class="mb-4 bg-azul-oscuro" style="width:100%; height: <?php echo $chartHeight; ?>px;"></div>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    // load charts with Spanish locale (change if you prefer another locale)
    google.charts.load('current',{packages:['corechart'], language: 'es'});
    google.charts.setOnLoadCallback(drawBarChart);

    function drawBarChart() {

        // Set Data
        const data = google.visualization.arrayToDataTable(<?php echo $chartData; ?>);

        // Format currency column (column index 1) for tooltips and labels
        // Change `currencyPrefix` to the desired symbol (e.g. '$', '€', '£')
        var currencyPrefix = '€';
        var formatter = new google.visualization.NumberFormat({
            suffix: currencyPrefix,
            fractionDigits: 2
        });
        formatter.format(data, 1); // apply to 2nd column (index 1)

        // Build custom ticks for hAxis so the currency symbol appears on axis labels
        var maxVal = 0;
        for (var r = 1; r < data.getNumberOfRows(); r++) {
            var v = Number(data.getValue(r, 1));
            if (!isNaN(v) && v > maxVal) maxVal = v;
        }
        var ticks = [];
        if (maxVal <= 0) {
            ticks = [{v:0, f: '0 ' + currencyPrefix}];
        } else {
            var ticksCount = 10;
            var step = Math.ceil(maxVal / ticksCount);
            // round step to a sensible round number (10/50/100)
            var magnitude = Math.pow(10, Math.floor(Math.log10(step)));
            step = Math.ceil(step / magnitude) * magnitude;
            for (var t = 0; t <= maxVal + 0.0001; t += step) {
                // format number with thousands separator using Spanish style
                var labelNumber = Math.round(t).toLocaleString('es-ES');
                // place the currency symbol after the number (e.g. "1.000 €")
                ticks.push({ v: t, f: labelNumber + ' ' + currencyPrefix });
                // safety: avoid infinite loops due to float rounding
                if (ticks.length > 20) break;
            }
            // ensure last tick includes maxVal
            if (ticks.length && ticks[ticks.length - 1].v < maxVal) {
                ticks.push({ v: maxVal, f: Math.round(maxVal).toLocaleString('es-ES') + ' ' + currencyPrefix });
            }
        }

        // Set Options (use dynamic left margin and height so labels fit)
        const options = {
            title: 'Total Income Per Product',
            titleTextStyle: { fontSize: 16, color: '#FDF0D5' },
            height: <?php echo $chartHeight; ?>,
            chartArea: { left: <?php echo $leftMargin; ?>, top: 60, width: '80%', height: '70%' },
            legend: { position: 'none' },
            hAxis: {
                minValue: 0,
                title: 'Total Income',
                titleTextStyle: { color: '#FDF0D5', fontSize: 12 },
                textStyle: { fontSize: 12, color: '#FDF0D5' },
                ticks: ticks
            },
            vAxis: { textStyle: { fontSize: 12, color: '#FDF0D5' }, title: 'Product Name', titleTextStyle: { color: '#FDF0D5', fontSize: 12 } },
            bar: { groupWidth: '70%' },
            backgroundColor: 'transparent',
            // ensure interactivity for tooltip
            enableInteractivity: true
        };

        // Draw
        const chart = new google.visualization.BarChart(document.getElementById('total-income-per-product'));
        chart.draw(data, options);

    }
</script>
