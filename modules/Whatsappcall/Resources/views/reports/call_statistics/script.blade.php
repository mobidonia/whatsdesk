@hasrole('owner')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Vue !== 'undefined' && document.getElementById('callStatisticsReport')) {
        new Vue({
            el: '#callStatisticsReport',
            data: {
                stats: {
                    totalCalls: 0,
                    answeredCalls: 0,
                    missedCalls: 0,
                    avgDuration: '0',
                    answerRate: 0,
                    missedRate: 0,
                    callsGrowth: 0
                },
                directionChart: null,
                statusChart: null,
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
                    
                    axios.get('/api/whatsappcall/reports/call-statistics')
                        .then(response => {
                            if (response.data.status === 'success') {
                                this.stats = response.data.data.stats;
                                this.$nextTick(() => {
                                    setTimeout(() => {
                                        this.initCharts(response.data.data);
                                    }, 100);
                                });
                            } else {
                                this.error = response.data.message || 'Unknown error';
                            }
                        })
                        .catch(error => {
                            console.error('Error loading call statistics report:', error);
                            this.error = error.response?.data?.message || 'Failed to load report data';
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                initCharts(data) {
                    this.initDirectionChart(data.directionData);
                    this.initStatusChart(data.statusData);
                },
                initDirectionChart(directionData) {
                    if (this.directionChart) {
                        this.directionChart.destroy();
                    }

                    const ctx = document.getElementById('callDirectionChart');
                    if (!ctx || !directionData || typeof Chart === 'undefined') {
                        console.warn('Direction chart: Canvas not found, no data, or Chart.js not loaded');
                        return;
                    }

                    console.log('Initializing call direction chart with data:', directionData);

                    this.directionChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Inbound (UIC)', 'Outbound (BIC)'],
                            datasets: [{
                                data: [directionData.inbound, directionData.outbound],
                                backgroundColor: ['#2dce89', '#5e72e4'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                }
                            },
                            cutout: '60%'
                        }
                    });
                },
                initStatusChart(statusData) {
                    if (this.statusChart) {
                        this.statusChart.destroy();
                    }

                    const ctx = document.getElementById('callStatusChart');
                    if (!ctx || !statusData || typeof Chart === 'undefined') {
                        console.warn('Status chart: Canvas not found, no data, or Chart.js not loaded');
                        return;
                    }

                    console.log('Initializing call status chart with data:', statusData);

                    const colors = {
                        'answered': '#2dce89',
                        'missed': '#fb6340',
                        'declined': '#f5365c',
                        'failed': '#ffd600',
                        'in_progress': '#5e72e4'
                    };

                    this.statusChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(statusData).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
                            datasets: [{
                                data: Object.values(statusData),
                                backgroundColor: Object.keys(statusData).map(status => colors[status] || '#6c757d'),
                                borderWidth: 0,
                                borderRadius: 4
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
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    } else {
        console.error('Vue is not loaded or element not found');
    }
});
</script>
@endhasrole
