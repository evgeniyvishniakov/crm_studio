( function ( $ ) {
  "use strict";

 // Flot Charts

 $.plot("#flotBar1", [{
  data: [[0, 3], [2, 8], [4, 5], [6, 13],[8,5], [10,7],[12,4], [14,6]],
  bars: {
    show: true,
    lineWidth: 0,
    fillColor: '#85c988'          
  }
}], {
  grid: {
    show: false,
    hoverable: true
  }
});


 $.plot("#flotBar2", [{
  data: [[0, 3], [2, 8], [4, 5], [6, 13],[8,5], [10,7],[12,4], [14,6]],
  bars: {
    show: true,
    lineWidth: 0,
    fillColor: '#f58f8d'
  }
}], {
  grid: {
    show: false
  }
});



 var plot = $.plot($('#flotLine1'),[{
  data: [[0, 1], [1, 3], [2,6], [3, 5], [4, 7], [5, 8], [6, 10]],
  color: '#fff'
}],
{
  series: {
    lines: {
      show: false
    },
    splines: {
      show: true,
      tension: 0.4,
      lineWidth: 2
        //fill: 0.4
      },
      shadowSize: 0
    },
    points: {
      show: false,
    },
    legend: {
      noColumns: 1,
      position: 'nw'
    },
    grid: {
      hoverable: true,
      clickable: true,
      show: false
    },
    yaxis: {
      min: 0,
      max: 10,
      color: '#eee',
      font: {
        size: 10,
        color: '#6a7074'
      }
    },
    xaxis: {
      color: '#eee',
      font: {
        size: 10,
        color: '#6a7074'
      }
    }
  });


 var plot = $.plot($('#flotLine2'),[{
  data: [[0, 8], [1, 5], [2,7], [3, 8], [4, 7], [5, 10], [6, 8], [7, 5], [8, 8], [9, 6], [10, 4]],
  label: 'New Data Flow',
  color: '#42a5f5'
}],
{
  series: {
    lines: {
      show: false
    },
    splines: {
      show: true,
      tension: 0.4,
      lineWidth: 1,
      fill: 0.25
    },
    shadowSize: 0
  },
  points: {
    show: false
  },
  legend: {
    show: false
  },
  grid: {
    show: false
  }
});

 var plot = $.plot($('#flotLine3'),[{
  data: [[0, 8], [1, 5], [2,7], [3, 8], [4, 7], [5, 10], [6, 8], [7, 5], [8, 8], [9, 6], [10, 4]],
  label: 'New Data Flow',
  color: '#ffa726'
}],
{
  series: {
    lines: {
      show: false
    },
    splines: {
      show: true,
      tension: 0.4,
      lineWidth: 1,
      fill: 0.25
    },
    shadowSize: 0
  },
  points: {
    show: false
  },
  legend: {
    show: false
  },
  grid: {
    show: false
  }
});

 var plot = $.plot($('#flotLine4'),[{
  data: [[0, 8], [1, 5], [2,7], [3, 8], [4, 7], [5, 10], [6, 8], [7, 5], [8, 8], [9, 6], [10, 4]],
  label: 'New Data Flow',
  color: '#5c6bc0'
}],
{
  series: {
    lines: {
      show: false
    },
    splines: {
      show: true,
      tension: 0.4,
      lineWidth: 1,
      fill: 0.25
    },
    shadowSize: 0
  },
  points: {
    show: false
  },
  legend: {
    show: false
  },
  grid: {
    show: false
  }
});


 var newCust = [[0, 3], [1, 5], [2,4], [3, 7], [4, 9], [5, 3], [6, 6], [7, 4], [8, 10]];

 var plot = $.plot($('#flotLine5'),[{
  data: newCust,
  label: 'New Data Flow',
  color: '#fff'
}],
{
  series: {
    lines: {
      show: true,
      lineColor: '#fff',
      lineWidth: 1
    },
    points: {
      show: true,
      fill: true,
      fillColor: "#ffffff",
      symbol: "circle",
      radius: 3
    },
    shadowSize: 0
  },
  points: {
    show: true,
  },
  legend: {
    show: false
  },
  grid: {
    show: false
  }
});


 /**************** PIE CHART *******************/
 var piedata = [
 { label: "Desktop visits", data: [[1,32]], color: '#5c6bc0'},
 { label: "Tab visits", data: [[1,33]], color: '#ef5350'},
 { label: "Mobile visits", data: [[1,35]], color: '#66bb6a'}
 ];

 $.plot('#flotPie1', piedata, {
  series: {
    pie: {
      show: true,
      radius: 1,
      innerRadius: 0.4,
      label: {
        show: true,
        radius: 2/3,
        threshold: 1
      },
      stroke: { 
        width: 0.1
      }
    }
  },
  grid: {
    hoverable: true,
    clickable: true
  }
});


// Real Time Chart


var data = [], totalPoints = 50;

function getRandomData() {
  if (data.length > 0)
    data = data.slice(1);
  while (data.length < totalPoints) {
    var prev = data.length > 0 ? data[data.length - 1] : 50,
    y = prev + Math.random() * 10 - 5;
    if (y < 0) {
      y = 0;
    } else if (y > 100) {
      y = 100;
    }
    data.push(y);
  }
  var res = [];
  for (var i = 0; i < data.length; ++i) {
    res.push([i, data[i]])
  }
  return res;
}


  // Set up the control widget
  var updateInterval = 1000;

  var plot5 = $.plot('#flotRealtime2', [ getRandomData() ], {
    colors: ['#5c6bc0'],

    series: {
      // label: 'Upload',
      lines: {
        show: true,
        lineWidth: 0,
        fill: 0.9
      },
      shadowSize: 0 // Drawing is faster without shadows
    },
    grid: {
      show: false
    },
    xaxis: {
      color: '#eee',
      font: {
        size: 10,
        color: '#6a7074'
      }
    },
    yaxis: {
      min: 0,
      max: 100,
      color: '#eee',
      font: {
        size: 10,
        color: '#6a7074'
      }
    }
  });

  function update_plot5() {
    plot5.setData([getRandomData()]);
    plot5.draw();
    setTimeout(update_plot5, updateInterval);
  }

  update_plot5();


// Traffic Chart

  if ($('#traffic-chart').length) {
    var chart = new Chartist.Line('#traffic-chart', {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      series: [
      [13000, 18000, 35000, 18000, 25000, 26000, 22000, 20000, 18000, 35000, 18000, 25000],
      [15000, 23000, 15000, 30000, 20000, 31000, 15000, 15000, 23000, 15000, 30000, 20000],
      [25000, 15000, 38000, 25500, 15000, 22500, 30000, 25000, 15000, 38000, 25500, 15000]
      ]
    }, {
      low: 0,
      showArea: true,
      showLine: false,
      showPoint: false,
      fullWidth: true,
      axisX: {
        showGrid: true
      }
    });

    chart.on('draw', function(data) {
      if(data.type === 'line' || data.type === 'area') {
        data.element.animate({
          d: {
            begin: 2000 * data.index,
            dur: 2000,
            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
            to: data.path.clone().stringify(),
            easing: Chartist.Svg.Easing.easeOutQuint
          }
        });
      }
    });
  }

/* Gauge Chart */

  var g1;

  document.addEventListener("DOMContentLoaded", function(event) {
    g1 = new JustGage({
      id: "g1",
      value: 72,
      //title: "Completed",
      fill: '#ffa726',
      symbol: '%',
      min: 0,
      max: 100,
      donut: true,
      gaugeWidthScale: 0.4,
      counter: true,
      hideInnerShadow: true
    });

  });

  /* Sparkline Tab Charts */

  $('#sparklinedash, #sparklinedash6, #sparklinedash11').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9], {
    type: 'bar',
    height: '30',
    barWidth: '5',
    disableHiddenCheck: true,
    resize: true,
    barSpacing: '2',
    barColor: '#42a5f5'
  });
  
  $('#sparklinedash2, #sparklinedash7, #sparklinedash12').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9], {
    type: 'bar',
    height: '30',
    barWidth: '5',
    resize: true,
    barSpacing: '2',
    barColor: '#ef5350'
  });
  $('#sparklinedash3, #sparklinedash8, #sparklinedash13').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9], {
    type: 'bar',
    height: '30',
    barWidth: '5',
    resize: true,
    barSpacing: '2',
    barColor: '#66bb6a'
  });
  $('#sparklinedash4, #sparklinedash9, #sparklinedash14').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9], {
    type: 'bar',
    height: '30',
    barWidth: '5',
    resize: true,
    barSpacing: '2',
    barColor: '#5c6bc0'
  });
  $('#sparklinedash5, #sparklinedash10, #sparklinedash15').sparkline([ 0, 5, 6, 10, 9, 12, 4, 9], {
    type: 'bar',
    height: '30',
    barWidth: '5',
    resize: true,
    barSpacing: '2',
    barColor: '#ffa726'
  });

 // Chartist

  var ctx = document.getElementById('area_chart').getContext('2d');

  var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'line',

        // The data for our dataset
        data: {
          labels: ["Jan", "Feb", "Mar", "Jun", "Jul", "Aug", "Sep"],
          datasets: [{
            label: "My First dataset",
            backgroundColor: 'transparent',
            borderColor: '#4fabf5',
            pointBackgroundColor: "#ffffff",
            data: [5000, 2700, 8500, 5500, 4500, 4900, 3000]
          },
          {
            label: "My Second dataset",
            backgroundColor: 'rgba(230,240,244,.5)',
            borderColor: '#6ebe73',
            pointBackgroundColor: "#ffffff",
            data: [5500, 2900, 7000, 3500, 5000, 3300, 4800 ]
          },
          {
            label: "My Third dataset",
            backgroundColor: 'transparent',
            borderColor: '#5c6bc0',
            pointBackgroundColor: "#ffffff",
            data: [2700, 7000, 3500, 6900, 2600, 6500, 2200]
          }]
        },

        // Configuration options go here
        options: {
          maintainAspectRatio: true,
          legend: {
            display: false
          },

          scales: {
            xAxes: [{
              display: true
            }],
            yAxes: [{
              display: true,
              gridLines: {
                zeroLineColor: '#e8e9ef',
                color: '#e8e9ef',
                drawBorder: true
              }
            }]

          },
          elements: {
            line: {
              tension: 0.00001,
              borderWidth: 1
            },
            point: {
              radius: 4,
              hitRadius: 10,
              hoverRadius: 4,
              borderWidth: 2
            }
          }
        }
      });




})( jQuery );


/*Knob*/

if (Gauge) {

  var opts = {
        lines: 12, // The number of lines to draw
        angle: 0, // The length of each line
        lineWidth: 0.05, // The line thickness
        pointer: {
            length: .75, // The radius of the inner circle
            strokeWidth: 0.03, // The rotation offset
            color: '#000' // Fill color
          },
        limitMax: 'true', // If true, the pointer will not go past the end of the gauge
        colorStart: '#42a5f5', // Colors
        colorStop: '#42a5f5', // just experiment with them
        strokeColor: '#fbfbfc', // to see which ones work best for you
        generateGradient: true
      };


    var target = document.getElementById('g2'); // your canvas element
    var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
    gauge.maxValue = 3000; // set max gauge value
    gauge.animationSpeed = 32; // set animation speed (32 is default value)
    gauge.set(1150); // set actual value
    //gauge.setTextField(document.getElementById("gauge-textfield"));

  }

// === Dashboard Scripts from Blade ===
let currentMetric = 'profit';
let currentPeriod = document.querySelector('.period-filters .tab-button.active')?.dataset.period || '30';

// Лейблы для разных периодов
const periodLabels = {
    7: ['6 дн', '5 дн', '4 дн', '3 дн', '2 дн', 'Вчера', 'Сегодня'],
    30: Array.from({length: 30}, (_, i) => `${30-i} дн назад`).reverse(),
    90: Array.from({length: 90}, (_, i) => `${90-i} дн назад`).reverse()
};

function getChartLabels(period) {
    if (period === '7') return getLastNDates(7);
    if (period === '30') return getMonthStartDates();
    if (period === '90') return getWeekStartDates(13);
    return [];
}

function getActivityDatasets(period) {
    return [
        {
            label: 'Клиенты',
            data: datasets.activity.data.clients[period],
            borderColor: '#8b5cf6',
            backgroundColor: '#8b5cf6' + '33',
            tension: 0.4,
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHitRadius: 12,
            spanGaps: true
        },
        {
            label: 'Записи',
            data: datasets.activity.data.appointments[period],
            borderColor: '#f59e0b',
            backgroundColor: '#f59e0b' + '33',
            tension: 0.4,
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHitRadius: 12,
            spanGaps: true
        },
        {
            label: 'Продажи услуг',
            data: datasets.activity.data.services[period],
            borderColor: '#8b5cf6',
            backgroundColor: '#8b5cf6' + '33',
            tension: 0.4,
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 6,
            pointHitRadius: 12,
            spanGaps: true
        }
    ];
}

let universalChart = null;

// Функция округления до красивого значения (500 или 1000)
function getNiceMax(value) {
    return Math.ceil(value / 1000) * 1000;
}

function createUniversalChart(type, labels, data, color, labelText) {
    const ctx = document.getElementById('universalChart').getContext('2d');
    if (universalChart) universalChart.destroy();

    // Определяем, что передали: массив datasets или массив чисел
    let datasets;
    if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && data[0].label) {
        datasets = data;
    } else {
        datasets = [{
            label: labelText,
            data: data,
            borderColor: color,
            backgroundColor: function(ctx) {
                const chart = ctx.chart;
                const {ctx:canvasCtx, chartArea} = chart;
                if (!chartArea) return color + (type === 'bar' ? '33' : '22');
                if (type === 'bar') {
                    if (labelText === 'Продажи товаров') {
                        const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        grad.addColorStop(0, 'rgba(59,130,246,0.18)');
                        grad.addColorStop(0.7, 'rgba(59,130,246,0.45)');
                        grad.addColorStop(1, 'rgba(59,130,246,0.85)');
                        return grad;
                    }
                    const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    grad.addColorStop(0, 'rgba(139,92,246,0.22)');
                    grad.addColorStop(0.7, 'rgba(139,92,246,0.45)');
                    grad.addColorStop(1, 'rgba(139,92,246,0.85)');
                    return grad;
                } else {
                    const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    grad.addColorStop(0, color + '33');
                    grad.addColorStop(1, color + '05');
                    return grad;
                }
            },
            tension: 0.4,
            fill: type !== 'bar',
            pointRadius: type === 'bar' ? 5 : 5,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 8,
            pointHitRadius: 14,
            spanGaps: true,
            borderRadius: type === 'bar' ? 8 : 0
        }];
    }

    universalChart = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            animation: { duration: 900, easing: 'easeOutQuart' },
            layout: { padding: { top: 32 } },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(59,130,246,0.95)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: color,
                    borderWidth: 1.5,
                    cornerRadius: 8,
                    padding: 12,
                    caretSize: 8,
                    displayColors: false
                },
                decimation: {
                    enabled: true,
                    algorithm: 'min-max'
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#22223b',
                        font: { size: 11, weight: '600' },
                        padding: 8,
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0,
                        stepSize: 1,
                        callback: function(value, index, ticks) {
                            if (currentPeriod === '90' || currentPeriod === '180' || currentPeriod === '365') {
                                return '';
                            }
                            if (currentPeriod === '7') return this.getLabelForValue(this.getLabels()[index]);
                            const date = new Date();
                            date.setDate(date.getDate() - (this.getLabels().length - 1 - index));
                            if (date.getDay() === 1) {
                                return this.getLabelForValue(this.getLabels()[index]);
                            }
                            return '';
                        }
                    }
                },
                y: {
                    grid: { color: '#e5e7eb', lineWidth: 1.2 },
                    ticks: { color: '#22223b', font: { size: 15, weight: '600' }, padding: 8 },
                    beginAtZero: true,
                    max: undefined
                }
            }
        }
    });
    
    // --- Устанавливаем максимум оси Y на красивое значение ---
    let allData = [];
    if (Array.isArray(datasets)) {
        datasets.forEach(ds => {
            if (Array.isArray(ds.data)) allData = allData.concat(ds.data);
        });
    }
    const maxValue = Math.max(...allData);
    let niceMax = maxValue > 0 ? getNiceMax(Math.ceil(maxValue * 1.10)) : 5000;
    universalChart.options.scales.y.max = niceMax;
    universalChart.update();
}

// По умолчанию активна кнопка месяц
document.querySelectorAll('.period-filters .tab-button').forEach(btn => {
    btn.classList.remove('active');
    if (btn.getAttribute('data-period') === '30') btn.classList.add('active');
});

// Инициализация universalChart только один раз при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/dashboard/profit-chart?period=30', { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
            universalChart.options.scales.y.max = res.maxValue || undefined;
            universalChart.update();
            renderCustomMonthLabels(res.labels);
        });
});

// Цвета для метрик
function getMetricColor(type) {
    const colors = {
        profit: '#10b981', // зелёный
        sales: '#3b82f6', // синий
        services: '#8b5cf6', // фиолетовый
        expenses: '#ef4444', // красный
        clients: '#8b5cf6', // фиолетовый
        appointments: '#f59e0b' // оранжевый
    };
    return colors[type] || '#10b981';
}

function getLastNDates(n) {
    const arr = [];
    const now = new Date();
    for (let i = n - 1; i >= 0; i--) {
        const d = new Date(now);
        d.setDate(now.getDate() - i);
        arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
    }
    return arr;
}

function getWeekStartDates(weeks) {
    const arr = [];
    const now = new Date();
    let monday = new Date(now);
    monday.setDate(now.getDate() - ((now.getDay() + 6) % 7));
    for (let i = weeks - 1; i >= 0; i--) {
        const d = new Date(monday);
        d.setDate(monday.getDate() - i * 7);
        arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
    }
    return arr;
}

// Возвращает массив дат от первого числа месяца до сегодня в формате 'дд МММ'
function getMonthStartDates() {
    const arr = [];
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    for (let i = 1; i <= now.getDate(); i++) {
        const d = new Date(year, month, i);
        arr.push(d.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' }).replace('.', ''));
    }
    return arr;
}

// Функция для получения накопительных данных
function getCumulativeData(arr) {
    let result = [];
    let sum = 0;
    for (let i = 0; i < arr.length; i++) {
        sum += arr[i];
        result.push(Number(sum.toFixed(2)));
    }
    return result;
}

// Добавляю функцию для генерации подписей месяцев
function getMonthLabels(n) {
    const arr = [];
    const now = new Date();
    let prevMonth = null;
    for (let i = n - 1; i >= 0; i--) {
        const d = new Date(now);
        d.setDate(now.getDate() - i);
        const month = d.toLocaleDateString('ru-RU', { month: 'short' });
        if (prevMonth !== month) {
            arr.push(month.charAt(0).toUpperCase() + month.slice(1));
            prevMonth = month;
        } else {
            arr.push('');
        }
    }
    return arr;
}

// --- Функция для обновления значений в карточках ---
function updateStatCardValue(type, value) {
    let selector = '';
    if (type === 'profit') selector = '.stat-card.profit-card .stat-value';
    if (type === 'expenses') selector = '.stat-card.expenses-card .stat-value';
    if (type === 'sales') selector = '.stat-card.sales-card .stat-value';
    if (type === 'services') selector = '.stat-card.services-card .stat-value';
    if (!selector) return;
    const card = document.querySelector(selector);
    if (card) {
        card.classList.remove('animated');
        // Используем систему валют
        card.className = 'stat-value currency-amount';
        card.setAttribute('data-amount', value);
        if (window.CurrencyManager) {
            card.textContent = window.CurrencyManager.formatAmount(value);
        } else {
            // Fallback если CurrencyManager не загружен
            card.textContent = new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' грн';
        }
        void card.offsetWidth;
        card.classList.add('animated');
    }
}

// --- Универсальная функция для обновления всех карточек по данным с сервера ---
function updateAllStatCardsByPeriod(period, animate = false, startDate = null, endDate = null) {
    // Прибыль
    let profitUrl = startDate && endDate
        ? `/api/dashboard/profit-chart?start_date=${startDate}&end_date=${endDate}`
        : `/api/dashboard/profit-chart?period=${period}`;
    fetch(profitUrl, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
            const card = document.querySelector('.stat-card.profit-card .stat-value');
            updateStatCardValue('profit', value);
        });
    // Расходы
    let expensesUrl = startDate && endDate
        ? `/api/dashboard/expenses-chart?start_date=${startDate}&end_date=${endDate}`
        : `/api/dashboard/expenses-chart?period=${period}`;
    fetch(expensesUrl, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
            const card = document.querySelector('.stat-card.expenses-card .stat-value');
            updateStatCardValue('expenses', value);
        });
    // Продажи товаров
    let salesUrl = startDate && endDate
        ? `/api/dashboard/sales-chart?start_date=${startDate}&end_date=${endDate}`
        : `/api/dashboard/sales-chart?period=${period}`;
    fetch(salesUrl, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
            const card = document.querySelector('.stat-card.sales-card .stat-value');
            updateStatCardValue('sales', value);
        });
    // Продажи услуг
    let servicesUrl = startDate && endDate
        ? `/api/dashboard/services-chart?start_date=${startDate}&end_date=${endDate}`
        : `/api/dashboard/services-chart?period=${period}`;
    fetch(servicesUrl, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            const value = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
            const card = document.querySelector('.stat-card.services-card .stat-value');
            updateStatCardValue('services', value);
        });
}

// Вставляю функцию в начало основного <script> блока, до всех fetch/then:
function renderCustomMonthLabels(labels) {
    const container = document.getElementById('custom-month-labels');
    if (!container) {
        
        return;
    }
    container.innerHTML = '';
    if (currentPeriod === '30' || !window.universalChart) {
        container.style.display = 'none';
        
        return;
    }
    
    
    // Устанавливаем стили контейнера
    container.style.display = 'flex';
    container.style.justifyContent = 'space-between';
    container.style.alignItems = 'center';
    container.style.height = '20px';
    container.style.marginTop = '-15px';
    container.style.fontSize = '12px';
    container.style.fontWeight = '600';
    container.style.color = '#22223b';
    container.style.position = 'relative';
    container.style.width = '80%';
    container.style.marginLeft = '120px';
    
    // Получаем координаты точек
    const meta = universalChart.getDatasetMeta(0);
    if (!meta || !meta.data) {
        
        return;
    }
    
    // Для столбчатых графиков используем центр столбца, для линейных - точку
    const points = meta.data.map(point => {
        if (universalChart.config.type === 'bar') {
            return point.x + (point.width / 2);
        }
        return point.x;
    });
    
    
    
    // Создаем метки каждые 3 недели
    labels.forEach((label, i) => {
        // Показываем только каждую третью метку (каждые 3 недели)
        if (i % 3 !== 0) return;
        
        let text = label;
        if (currentPeriod === '365') {
            const monthNum = label.split('.')[0];
            const months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
            const idx = parseInt(monthNum, 10) - 1;
            text = months[idx] ?? label;
        } else {
            // Для периодов 90/180 дней показываем только дату начала недели
            // Убираем диапазон и оставляем только первую дату
            text = label.split(' - ')[0];
        }
        
        const span = document.createElement('span');
        span.textContent = text;
        span.style.position = 'absolute';
        
        // Используем процентное позиционирование вместо абсолютных координат
        const percentage = (i / (labels.length - 1)) * 100;
        span.style.left = percentage + '%';
        span.style.transform = 'translateX(-50%)';
        
        span.style.fontSize = '11px';
        span.style.color = '#22223b';
        span.style.pointerEvents = 'none';
        span.style.zIndex = '1000';
        span.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
        span.style.padding = '1px 3px';
        span.style.borderRadius = '2px';
        span.style.fontWeight = '600';
        span.style.whiteSpace = 'nowrap';
        container.appendChild(span);
        
    });
}

// Dropdown логика
const metricToggle = document.querySelector('.metric-toggle');
const metricMenu = document.querySelector('.metric-menu');
const metricDropdown = document.querySelector('.metric-dropdown');
const selectedMetricLabel = document.getElementById('selectedMetricLabel');

if (metricToggle) {
    metricToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        metricDropdown.classList.toggle('open');
    });
}

document.addEventListener('click', function() {
    if (metricDropdown) {
        metricDropdown.classList.remove('open');
    }
});

document.querySelectorAll('.metric-item').forEach(item => {
    item.addEventListener('click', function() {
        const type = this.dataset.type;
        currentMetric = type;
        selectedMetricLabel.textContent = datasets[type].label;
        metricToggle.querySelector('i').className = 'fas ' + datasets[type].icon;

        // Если выбран диапазон дат — обновляем по диапазону
        if (selectedRange && selectedRange.start && selectedRange.end) {
            const formatISO = d => d.toISOString().slice(0, 10);
            updateChartByRange(formatISO(selectedRange.start), formatISO(selectedRange.end));
            return;
        }

        // Если нет — по period (старая логика)
        if (type === 'profit') {
            fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                    universalChart.options.scales.y.max = res.maxValue || undefined;
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (type === 'expenses') {
            fetch(`/api/dashboard/expenses-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const data = getCumulativeData(res.data);
                    let labels = res.labels;
                    createUniversalChart('line', labels, data, getMetricColor('expenses'), datasets['expenses'].label);
                    renderCustomMonthLabels(labels);
                });
            return;
        }
        if (type === 'sales') {
            fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('bar', res.labels, res.data, getMetricColor('sales'), 'Продажи товаров');
                    universalChart.update();
                    // Обновляем карточку "Продажи товаров" без анимации
                    const salesCard = document.querySelector('.stat-card.sales-card .stat-value');
                    if (salesCard && Array.isArray(res.data)) {
                        const total = res.data.reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
                        // Используем систему валют
                        salesCard.className = 'stat-value currency-amount';
                        salesCard.setAttribute('data-amount', total);
                        if (window.CurrencyManager) {
                            salesCard.textContent = window.CurrencyManager.formatAmount(total);
                        } else {
                            salesCard.textContent = new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total) + ' грн';
                        }
                    }
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (type === 'services') {
            fetch(`/api/dashboard/services-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('bar', res.labels, res.data, getMetricColor('services'), 'Продажи услуг');
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        // Для других метрик по умолчанию line
        fetch(`/api/dashboard/${type}-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(res => {
                createUniversalChart('line', res.labels, res.data, getMetricColor(type), datasets[type].label);
                universalChart.update();
                renderCustomMonthLabels(res.labels);
            });
    });
});

// Фильтры периода
document.querySelectorAll('.period-filters .tab-button').forEach(btn => {
    btn.addEventListener('click', function() {
        if (btn.id === 'dateRangePicker') return;
        document.querySelectorAll('.period-filters .tab-button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        if (window.calendarRangeDisplay) {
            window.calendarRangeDisplay.textContent = '';
        }
        window.selectedRange = null;
        currentPeriod = this.dataset.period;
        // Обновляем данные для текущей метрики
        if (currentMetric === 'profit') {
            fetch(`/api/dashboard/profit-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                    universalChart.options.scales.y.max = res.maxValue || undefined;
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (currentMetric === 'expenses') {
            fetch(`/api/dashboard/expenses-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    const data = getCumulativeData(res.data);
                    createUniversalChart('line', res.labels, data, getMetricColor('expenses'), 'Расходы');
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (currentMetric === 'sales') {
            fetch(`/api/dashboard/sales-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('bar', res.labels, res.data, getMetricColor('sales'), 'Продажи товаров');
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
        if (currentMetric === 'services') {
            fetch(`/api/dashboard/services-chart?period=${currentPeriod}`, { credentials: 'same-origin' })
                .then(res => res.json())
                .then(res => {
                    createUniversalChart('bar', res.labels, res.data, getMetricColor('services'), 'Продажи услуг');
                    universalChart.update();
                    renderCustomMonthLabels(res.labels);
                });
            return;
        }
    });
});

// Переключение вкладок Финансы/Активность
document.querySelectorAll('.dashboard-tabs .tab-button').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.dashboard-tabs .tab-button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const tab = this.getAttribute('data-tab');
        
        // Скрываем все карточки активности
        document.querySelectorAll('.stat-card.activity-group').forEach(card => {
            card.style.display = 'none';
        });
        
        // Скрываем карточки финансов (кроме прибыли)
        document.querySelectorAll('.stat-card.finances-group:not(.profit-card)').forEach(card => {
            card.style.display = 'none';
        });
        
        // Показываем карточки выбранной группы
        if (tab === 'finances') {
            document.querySelectorAll('.stat-card.finances-group').forEach(card => {
                card.style.display = 'flex';
            });
        } else if (tab === 'activity') {
            document.querySelectorAll('.stat-card.activity-group').forEach(card => {
                card.style.display = 'flex';
            });
            // Прибыль всегда видна
            document.querySelector('.stat-card.profit-card').style.display = 'flex';
        }
    });
});

// --- Вызов при загрузке страницы (по умолчанию месяц) ---
document.addEventListener('DOMContentLoaded', function() {
    // Синхронизируем currentPeriod с активной кнопкой
    currentPeriod = document.querySelector('.period-filters .tab-button.active')?.dataset.period || '30';
    updateAllStatCardsByPeriod(currentPeriod, false); // false — без анимации
});

// Данные для графика (пример)
const datasets = {
    profit: {
        label: "Прибыль",
        icon: "fa-coins",
        data: {
            7: [18000, 17500, 17000, 16800, 16500, 16200, 16000],
            30: [10000, 12000, 15000, 14000, 16000, 18000, 17500, 17000, 16800, 16500, 16200, 16000, 15800, 15500, 15300, 15000, 14800, 14500, 14300, 14000, 13800, 13500, 13300, 13000, 12800, 12500, 12300, 12000, 11800, 11500],
            90: [10000, 10500, 11000, 11500, 12000, 12500, 13000, 13500, 14000, 14500, 15000, 15500, 16000, 16500, 17000, 17500, 18000, 18500, 19000, 19500, 20000, 20500, 21000, 21500, 22000, 22500, 23000, 23500, 24000, 24500, 25000, 25500, 26000, 26500, 27000, 27500, 28000, 28500, 29000, 29500, 30000, 30500, 31000, 31500, 32000, 32500, 33000, 33500, 34000, 34500, 35000, 35500, 36000, 36500, 37000, 37500, 38000, 38500, 39000, 39500, 40000, 40500, 41000, 41500, 42000, 42500, 43000, 43500, 44000, 44500, 45000, 45500, 46000, 46500, 47000, 47500, 48000, 48500, 49000, 49500]
        }
    }
};


function updateChartByRange(startDate, endDate) {
    updateAllStatCardsByPeriod(null, false, startDate, endDate);
    // ... остальной код ...
}

// === FullCalendar: минималистичный календарь с точками по статусу ===
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('dashboardCalendar')) {
        const calendarEl = document.getElementById('dashboardCalendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ru',
            height: 'auto',
            firstDay: 1,
            headerToolbar: false,
            events: '/appointments/calendar-events',

            eventDidMount: function(info) {
                // Получаем дату текущего события
                const currentEventDate = info.event.start ? info.event.start.toISOString().slice(0,10) : null;
                
                // Получаем все события на этот день
                const dayEvents = calendar.getEvents().filter(ev => {
                    const evDate = ev.start ? ev.start.toISOString().slice(0,10) : null;
                    return evDate === currentEventDate;
                });
                
                // Создаем индикатор для всех дней с записями
                if (dayEvents.length > 0) {
                    const dayEl = info.el.closest('.fc-daygrid-day');
                    if (dayEl) {
                        // Проверяем, есть ли уже индикатор
                        let existingIndicator = dayEl.querySelector('.appointment-count-indicator');
                        
                        if (!existingIndicator) {
                            // Создаем новый индикатор
                            const indicator = document.createElement('div');
                            indicator.className = 'appointment-count-indicator';
                            indicator.textContent = dayEvents.length;
                            dayEl.style.position = 'relative';
                            dayEl.appendChild(indicator);
                        }
                    }
                }
                
                // Скрываем все точки
                const dotEl = info.el.querySelector('.fc-daygrid-event-dot');
                if (dotEl) {
                    dotEl.style.display = 'none';
                }
            },

            dateClick: function(info) {
                // Открыть модалку с событиями на этот день
                showDayModal(info.dateStr, calendar.getEvents());
            },

            eventClick: function(info) {
                info.jsEvent.preventDefault(); // Предотвращаем стандартное поведение
                const dateStr = info.event.startStr.slice(0, 10);
                showDayModal(dateStr, calendar.getEvents());
            },

            datesSet: function() {
                updateCalendarTitle(this); // `this` is the calendar instance
            }
        });

        function updateCalendarTitle(calInstance) {
            const titleEl = document.getElementById('calendarMonthYearTitle');
            if (titleEl) {
                let title = calInstance.view.title;
                titleEl.textContent = title.charAt(0).toUpperCase() + title.slice(1);
            }
        }

        calendar.render();
        updateCalendarTitle(calendar); // Set initial title

        document.getElementById('calendarPrevBtn').addEventListener('click', function() {
            calendar.prev();
        });

        document.getElementById('calendarNextBtn').addEventListener('click', function() {
            calendar.next();
        });

        // Обработчик для кнопки "Добавить новую" в виджете "Записи"
        const addWidgetBtn = document.getElementById('addWidgetAppointmentBtn');
        if (addWidgetBtn) {
            addWidgetBtn.addEventListener('click', function() {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayDateStr = `${year}-${month}-${day}`;
                window.location.href = '/appointments?action=create&date=' + todayDateStr;
            });
        }
    }
});

// Цвет точки по статусу
function getStatusColor(status) {
    switch (status) {
        case 'done':
        case 'completed': return '#10b981';      // зелёный
        case 'pending': return '#f59e0b';        // оранжевый
        case 'cancelled': return '#ef4444';      // красный
        case 'rescheduled': return '#3b82f6';    // синий
        default: return '#cbd5e1';               // серый
    }
}

// Модалка для событий дня
function showDayModal(dateStr, allEvents) {
    const modal = document.getElementById('calendarDayModal');
    const title = document.getElementById('modalDayTitle');
    const eventsBlock = document.getElementById('modalDayEvents');
    const addBtn = document.getElementById('modalAddAppointmentBtn');
    const closeBtn = document.getElementById('closeDayModalBtn');
    // Форматируем дату
    const d = new Date(dateStr);
    title.textContent = 'Записи на день ' + d.toLocaleDateString('ru-RU');
    // Фильтруем события по дате
    const events = allEvents.filter(ev => {
        const evDate = ev.extendedProps.date || (ev.start ? ev.start.toISOString().slice(0,10) : null);
        return evDate === dateStr;
    });
    if (events.length === 0) {
        eventsBlock.innerHTML = '<div class="calendar-modal-empty">Нет записей на этот день</div>';
    } else {
        eventsBlock.innerHTML = events.map(ev => {
            const time = ev.extendedProps.time ? ev.extendedProps.time.slice(0, 5) : (ev.start ? new Date(ev.start).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' }) : '');
            // Получаем статус
            const status = ev.extendedProps.status || ev.status || 'pending';
            let statusName = '';
            let statusClass = '';
            switch (status) {
                case 'completed': statusName = 'Завершено'; statusClass = 'done'; break;
                case 'pending': statusName = 'Ожидает'; statusClass = 'pending'; break;
                case 'cancelled': statusName = 'Отменено'; statusClass = 'cancel'; break;
                case 'rescheduled': statusName = 'Перенесено'; statusClass = 'rescheduled'; break;
                default: statusName = status; statusClass = 'default'; break;
            }
            return `<div class="calendar-modal-event-item">
                <span class='fc-dot' style='background:${getStatusColor(status)}'></span>
                <span><b>${time}</b> ${ev.extendedProps.client || ''} <span class="calendar-modal-service-name">(${ev.extendedProps.service || ''})</span></span>
                <span class="status-badge status-${statusClass} calendar-modal-status-badge">${statusName}</span>
            </div>`
        }).join('');
    }
    modal.style.display = 'flex';
    // Кнопка "Добавить новую"
    addBtn.onclick = function() {
        window.location.href = '/appointments?action=create&date=' + dateStr;
    };
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };
    // Модальные окна теперь закрываются только по кнопкам
    // Убираем автоматическое закрытие при клике вне модального окна
}

// === Календарь выбора диапазона дат для графика ===
const calendarBtn = document.getElementById('dateRangePicker');
const calendarRangeDisplay = document.getElementById('calendarRangeDisplay');
let calendarInstance = null;
let selectedRange = null;

if (calendarBtn) {
    calendarBtn.addEventListener('click', function (e) {
        if (!calendarInstance) {
            calendarInstance = flatpickr(calendarBtn, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'ru',
                onClose: function (selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        document.querySelectorAll('.period-filters .tab-button').forEach(btn => btn.classList.remove('active'));
                        calendarBtn.classList.add('active');
                        const format = d => d.toLocaleDateString('ru-RU', {day: '2-digit', month: '2-digit'});
                        calendarRangeDisplay.textContent = `${format(selectedDates[0])} — ${format(selectedDates[1])}`;
                        selectedRange = {start: selectedDates[0], end: selectedDates[1]};
                        // Форматируем для запроса
                        const formatISO = d => d.toISOString().slice(0, 10);
                        // Обновляем график для текущей метрики
                        updateChartByRange(formatISO(selectedDates[0]), formatISO(selectedDates[1]));
                    }
                }
            });
        }
        calendarInstance.open();
    });
}

function updateChartByRange(startDate, endDate) {
    updateAllStatCardsByPeriod(null, true, startDate, endDate);
    // Определяем текущую метрику
    if (currentMetric === 'profit') {
        fetch(`/api/dashboard/profit-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(res => {
                createUniversalChart('line', res.labels, getCumulativeData(res.data), getMetricColor('profit'), 'Прибыль');
                universalChart.options.scales.y.max = res.maxValue || undefined;
                universalChart.update();
                renderCustomMonthLabels(res.labels);
            });
        return;
    }
    if (currentMetric === 'expenses') {
        fetch(`/api/dashboard/expenses-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(res => {
                const data = getCumulativeData(res.data);
                createUniversalChart('line', res.labels, data, getMetricColor('expenses'), 'Расходы');
                renderCustomMonthLabels(res.labels);
            });
        return;
    }
    if (currentMetric === 'sales') {
        fetch(`/api/dashboard/sales-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(res => {
                createUniversalChart('bar', res.labels, res.data, getMetricColor('sales'), 'Продажи товаров');
                universalChart.update();
                renderCustomMonthLabels(res.labels);
            });
        return;
    }
    if (currentMetric === 'services') {
        fetch(`/api/dashboard/services-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
            .then(res => res.json())
            .then(res => {
                createUniversalChart('bar', res.labels, res.data, getMetricColor('services'), 'Продажи услуг');
                universalChart.update();
                renderCustomMonthLabels(res.labels);
            });
        return;
    }
    // Для других метрик по умолчанию line
    fetch(`/api/dashboard/${currentMetric}-chart?start_date=${startDate}&end_date=${endDate}`, { credentials: 'same-origin' })
        .then(res => res.json())
        .then(res => {
            createUniversalChart('line', res.labels, res.data, getMetricColor(currentMetric), datasets[currentMetric].label);
            universalChart.update();
            renderCustomMonthLabels(res.labels);
        });
}

// === ToDo List Logic ===
document.addEventListener('DOMContentLoaded', function() {
    const todoListContainer = document.getElementById('todoListContainer');
    const newTodoInput = document.getElementById('newTodoInput');
    const addTodoBtn = document.getElementById('addTodoBtn');

    if (!todoListContainer || !newTodoInput || !addTodoBtn) return;

    let todos = JSON.parse(localStorage.getItem('dashboard_todos')) || [];

    function saveTodos() {
        localStorage.setItem('dashboard_todos', JSON.stringify(todos));
    }

    function renderTodos() {
        todoListContainer.innerHTML = '';
        if (todos.length === 0) {
            todoListContainer.innerHTML = `<li class="todo-empty-state">Нет задач</li>`;
            return;
        }
        todos.forEach((todo, index) => {
            const li = document.createElement('li');
            li.className = todo.done ? 'done' : '';
            li.dataset.index = index;
            li.innerHTML = `
                <span class="todo-drag"><i class="fas fa-grip-lines"></i></span>
                <input type="checkbox" id="todo-${index}" ${todo.done ? 'checked' : ''}>
                <label for="todo-${index}">${todo.text}</label>
                <span class="todo-actions">
                    <i class="fas fa-trash delete-btn"></i>
                </span>
            `;
            todoListContainer.appendChild(li);
        });
    }

    function addTodo() {
        const text = newTodoInput.value.trim();
        if (text) {
            todos.push({ text: text, done: false });
            newTodoInput.value = '';
            saveTodos();
            renderTodos();
        }
    }

    addTodoBtn.addEventListener('click', addTodo);
    newTodoInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            addTodo();
        }
    });

    todoListContainer.addEventListener('click', function(e) {
        const target = e.target;
        const li = target.closest('li');
        if (!li || li.classList.contains('todo-empty-state')) return;
        const index = li.dataset.index;

        // Toggle done
        if (target.type === 'checkbox') {
            todos[index].done = target.checked;
            saveTodos();
            renderTodos();
        }

        // Delete todo
        if (target.classList.contains('delete-btn')) {
            todos.splice(index, 1);
            saveTodos();
            renderTodos();
        }
    });

    renderTodos(); // Initial render

    // Инициализация SortableJS для перетаскивания
    if (window.Sortable) {
        new Sortable(todoListContainer, {
            animation: 150,
            handle: '.todo-drag', // Указываем, за какой элемент можно перетаскивать
            onEnd: function (evt) {
                // Обновляем массив `todos` в соответствии с новым порядком
                const movedItem = todos.splice(evt.oldIndex, 1)[0];
                todos.splice(evt.newIndex, 0, movedItem);

                // Сохраняем новый порядок и перерисовываем список
                saveTodos();
                renderTodos();
            }
        });
    }
});

// === Современный стиль для universalChart ===
document.addEventListener('DOMContentLoaded', function() {
    // --- Современный стиль для universalChart ---
    if (window.Chart && Chart.defaults && Chart.defaults.scales) {
        Chart.defaults.scales.x = Chart.defaults.scales.x || {};
        Chart.defaults.scales.x.grid = Chart.defaults.scales.x.grid || {};
        Chart.defaults.scales.x.grid.display = false;
        Chart.defaults.scales.y = Chart.defaults.scales.y || {};
        Chart.defaults.scales.y.grid = Chart.defaults.scales.y.grid || {};
        Chart.defaults.scales.y.grid.color = '#e5e7eb';
        Chart.defaults.scales.y.grid.lineWidth = 1.2;
        Chart.defaults.scales.x.ticks = Chart.defaults.scales.x.ticks || {};
        Chart.defaults.scales.x.ticks.padding = 8;
        Chart.defaults.scales.y.ticks = Chart.defaults.scales.y.ticks || {};
        Chart.defaults.scales.y.ticks.padding = 8;
        Chart.defaults.font = Chart.defaults.font || {};
        Chart.defaults.font.family = 'Inter, Arial, sans-serif';
        Chart.defaults.font.size = 15;
        Chart.defaults.color = '#22223b';
        Chart.defaults.plugins = Chart.defaults.plugins || {};
        Chart.defaults.plugins.legend = Chart.defaults.plugins.legend || {};
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.plugins.tooltip = Chart.defaults.plugins.tooltip || {};
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(59,130,246,0.95)';
        Chart.defaults.plugins.tooltip.titleColor = '#fff';
        Chart.defaults.plugins.tooltip.bodyColor = '#fff';
        Chart.defaults.plugins.tooltip.borderColor = '#3b82f6';
        Chart.defaults.plugins.tooltip.borderWidth = 1.5;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.caretSize = 8;
        Chart.defaults.plugins.tooltip.displayColors = false;
        Chart.defaults.elements = Chart.defaults.elements || {};
        Chart.defaults.elements.line = Chart.defaults.elements.line || {};
        Chart.defaults.elements.line.tension = 0.4;
        Chart.defaults.elements.line.borderWidth = 3;
        Chart.defaults.elements.line.borderColor = 'rgba(59,130,246,1)';
        Chart.defaults.elements.line.backgroundColor = function(ctx) {
            const chart = ctx.chart;
            const {ctx:canvasCtx, chartArea} = chart;
            if (!chartArea) return 'rgba(59,130,246,0.12)';
            const grad = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            grad.addColorStop(0, 'rgba(59,130,246,0.18)');
            grad.addColorStop(1, 'rgba(59,130,246,0.01)');
            return grad;
        };
        Chart.defaults.elements.point = Chart.defaults.elements.point || {};
        Chart.defaults.elements.point.radius = 5;
        Chart.defaults.elements.point.backgroundColor = '#3b82f6';
        Chart.defaults.elements.point.borderColor = '#fff';
        Chart.defaults.elements.point.borderWidth = 2;
        Chart.defaults.elements.point.hoverRadius = 8;
        Chart.defaults.elements.bar = Chart.defaults.elements.bar || {};
        Chart.defaults.elements.bar.borderRadius = 8;
        Chart.defaults.elements.bar.backgroundColor = function(ctx) {
            const chart = ctx.chart;
            const {ctx:canvasCtx, chartArea} = chart;
            if (!chartArea) return 'rgba(139,92,246,0.18)';
            const grad = canvasCtx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
            grad.addColorStop(0, 'rgba(139,92,246,0.18)');
            grad.addColorStop(0.5, 'rgba(139,92,246,0.35)');
            grad.addColorStop(1, 'rgba(139,92,246,0.7)');
            return grad;
        };
        Chart.defaults.animation = Chart.defaults.animation || {};
        Chart.defaults.animation.duration = 900;
        Chart.defaults.animation.easing = 'easeOutQuart';
    }
});