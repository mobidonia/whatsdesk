<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Vue !== 'undefined' && document.getElementById('conversationTrafficReport')) {
        new Vue({
            el: '#conversationTrafficReport',
            data: {
                stats: {
                    totalMessages: 0,
                    newConversations: 0,
                    avgResponseTime: '0',
                    resolutionRate: 0,
                    resolvedConversations: 0,
                    messagesGrowth: 0,
                    conversationsGrowth: 0
                },
                chartData: [],
                chart: null,
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
                    
                    axios.get('/api/wpbox/reports/conversation-traffic', {
                        params: {
                            period: this.selectedPeriod
                        }
                    })
                    .then(response => {
                        if (response.data.status === 'success') {
                            this.stats = response.data.data.stats;
                            this.chartData = response.data.data.chartData;
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.initChart();
                                }, 100);
                            });
                        } else {
                            this.error = response.data.message || 'Unknown error';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading conversation traffic report:', error);
                        this.error = error.response?.data?.message || 'Failed to load report data';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                },
                initChart() {
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    const ctx = document.getElementById('conversationTrafficChart');
                    if (!ctx) {
                        console.warn('Canvas element not found');
                        return;
                    }
                    if (!this.chartData.length) {
                        console.warn('No chart data available');
                        return;
                    }
                    if (typeof Chart === 'undefined') {
                        console.warn('Chart.js not loaded');
                        return;
                    }
                    
                    console.log('Initializing conversation traffic chart with data:', this.chartData);

                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.chartData.map(item => item.date),
                            datasets: [{
                                label: 'Messages',
                                data: this.chartData.map(item => item.messages),
                                borderColor: '#5e72e4',
                                backgroundColor: 'rgba(94, 114, 228, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'New Conversations',
                                data: this.chartData.map(item => item.conversations),
                                borderColor: '#2dce89',
                                backgroundColor: 'rgba(45, 206, 137, 0.1)',
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
                                        color: '#fff'
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
                }
            }
        });
    }else{
        console.error('Vue is not loaded');
    }
});
</script>
