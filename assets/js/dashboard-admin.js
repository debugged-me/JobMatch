(function () {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function parseJson(value, fallback) {
    if (!value) return fallback;
    try {
      return JSON.parse(value);
    } catch (e) {
      return fallback;
    }
  }

  function init() {
    var ctx = document.getElementById('hiresChart');
    if (!ctx || typeof Chart === 'undefined') return;

    var labels = parseJson(ctx.dataset.labels, []);
    var values = parseJson(ctx.dataset.values, []);

    var brandRedRGB = '193,39,45';
    new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Hires',
          data: values,
          fill: true,
          tension: 0.35,
          borderWidth: 2,
          borderColor: 'rgba(' + brandRedRGB + ',1)',
          backgroundColor: function (c) {
            var g = c.chart.ctx.createLinearGradient(0, 0, 0, 240);
            g.addColorStop(0, 'rgba(' + brandRedRGB + ',0.20)');
            g.addColorStop(1, 'rgba(' + brandRedRGB + ',0.05)');
            return g;
          }
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#6B7280'
            }
          },
          y: {
            grid: {
              color: '#F3F4F6'
            },
            ticks: {
              color: '#6B7280',
              precision: 0
            }
          }
        }
      }
    });
  }
})();
