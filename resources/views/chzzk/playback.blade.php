<!DOCTYPE html>
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<video id="videoPlayer" controls src="{{ $video }}" width="640"></video>
<canvas id="timelineChart" width="640" height="40"></canvas>
<script>
const raw = @json($timeline);
const chatMap = {};
raw.forEach(d => chatMap[d.time] = d.chat);

const video = document.getElementById('videoPlayer');
video.addEventListener('loadedmetadata', () => {
    const duration = Math.floor(video.duration);
    const labels = Array.from({length: duration}, (_, i) => i);
    const chatData = labels.map(sec => chatMap[sec] || 0);

    const sorted = [...chatData].sort((a, b) => a - b);
    const p95 = sorted[Math.floor(sorted.length * 0.95)] || 0;
    const p90 = sorted[Math.floor(sorted.length * 0.90)] || 0;
    const colors = chatData.map(v => {
        if (v >= p95) return 'red';
        if (v >= p90) return 'yellow';
        if (p90 === 0) return '#eeeeee';
        const intensity = Math.round(238 - (v / p90) * (238 - 102));
        return `rgb(${intensity},${intensity},${intensity})`;
    });

    const progressPlugin = {
        id: 'progressLine',
        afterDraw(chart, args, opts) {
            const {ctx, chartArea: {top, bottom}} = chart;
            const x = chart.scales.x.getPixelForValue(video.currentTime);
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, top);
            ctx.lineTo(x, bottom);
            ctx.lineWidth = 2;
            ctx.strokeStyle = 'rgba(0,0,255,0.7)';
            ctx.stroke();
            ctx.restore();
        }
    };
    Chart.register(progressPlugin);

    const chart = new Chart(document.getElementById('timelineChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: new Array(duration).fill(1),
                backgroundColor: colors,
                borderWidth: 0,
                barPercentage: 1.0,
                categoryPercentage: 1.0
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
                progressLine: {}
            },
            animation: false,
            scales: {
                x: { display: false },
                y: { display: false, max: 1 }
            }
        }
    });

    video.addEventListener('timeupdate', () => chart.draw());

    document.getElementById('timelineChart').onclick = function(evt) {
        const points = chart.getElementsAtEventForMode(evt, 'nearest', {intersect: true}, true);
        if (points.length) {
            const idx = points[0].index;
            video.currentTime = idx;
        }
    };
});
</script>
</body>
</html>
