@hasrole('owner')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Vue !== 'undefined' && document.getElementById('callPerformanceReport')) {
        new Vue({
            el: '#callPerformanceReport',
            data: {
                stats: {
                    peakCallTime: '-',
                    peakCallCount: 0,
                    successRate: 0,
                    successfulCalls: 0,
                    avgWaitTime: '0'
                },
                recentCalls: [],
                performanceChart: null,
                selectedPeriod: '7',
                loading: true,
                error: null
            },
            mounted() {
                this.loadReportData();
            },
            methods: {
                loadReportData() {
                    this.loading = true;
                    this.error = null;
                    
                    axios.get('/api/whatsappcall/reports/call-performance', {
                        params: {
                            period: this.selectedPeriod
                        }
                    })
                    .then(response => {
                        if (response.data.status === 'success') {
                            this.stats = response.data.data.stats;
                            this.recentCalls = response.data.data.recentCalls;
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.initPerformanceChart(response.data.data.chartData);
                                }, 100);
                            });
                        } else {
                            this.error = response.data.message || 'Unknown error';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading call performance report:', error);
                        this.error = error.response?.data?.message || 'Failed to load report data';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                },
                initPerformanceChart(chartData) {
                    if (this.performanceChart) {
                        this.performanceChart.destroy();
                    }

                    const ctx = document.getElementById('callPerformanceChart');
                    if (!ctx || !chartData || !chartData.length || typeof Chart === 'undefined') {
                        console.warn('Performance chart: Canvas not found, no data, or Chart.js not loaded');
                        return;
                    }

                    console.log('Initializing call performance chart with data:', chartData);

                    this.performanceChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.map(item => item.date),
                            datasets: [{
                                label: 'Total Calls',
                                data: chartData.map(item => item.total),
                                borderColor: '#5e72e4',
                                backgroundColor: 'rgba(94, 114, 228, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Answered Calls',
                                data: chartData.map(item => item.answered),
                                borderColor: '#2dce89',
                                backgroundColor: 'rgba(45, 206, 137, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Missed Calls',
                                data: chartData.map(item => item.missed),
                                borderColor: '#fb6340',
                                backgroundColor: 'rgba(251, 99, 64, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: '#fff',
                                        stepSize: 1
                                    }
                                },
                                x: {
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: '#fff'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#fff'
                                    }
                                }
                            }
                        }
                    });
                },
                formatDateTime(dateTime) {
                    if (!dateTime) return '-';
                    return new Date(dateTime).toLocaleString();
                },
                formatStatus(status) {
                    return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
                },
                getStatusBadgeClass(status) {
                    const statusClasses = {
                        'answered': 'badge-success',
                        'accept': 'badge-success',
                        'in_progress': 'badge-info',
                        'connect': 'badge-info',
                        'missed': 'badge-warning',
                        'declined': 'badge-danger',
                        'failed': 'badge-danger',
                        'ended': 'badge-secondary',
                        'terminate': 'badge-secondary'
                    };
                    return statusClasses[status] || 'badge-secondary';
                }
            }
        });
    } else {
        console.error('Vue is not loaded or element not found');
    }
});
</script>
@endhasrole