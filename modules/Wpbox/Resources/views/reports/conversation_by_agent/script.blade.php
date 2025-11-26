<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Vue !== 'undefined' && document.getElementById('conversationByAgentReport')) {
        new Vue({
            el: '#conversationByAgentReport',
            data: {
                agentStats: [],
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
                    
                    axios.get('/api/wpbox/reports/conversation-by-agent', {
                        params: {
                            period: this.selectedPeriod
                        }
                    })
                    .then(response => {
                        if (response.data.status === 'success') {
                            this.agentStats = response.data.data.agents;
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
                        console.error('Error loading conversation by agent report:', error);
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

                    const ctx = document.getElementById('agentPerformanceChart');
                    if (!ctx) {
                        console.warn('Canvas element not found');
                        return;
                    }
                    if (!this.agentStats.length) {
                        console.warn('No agent data available');
                        return;
                    }
                    if (typeof Chart === 'undefined') {
                        console.warn('Chart.js not loaded');
                        return;
                    }
                    
                    console.log('Initializing agent performance chart with data:', this.agentStats);

                    const colors = ['#5e72e4', '#2dce89', '#fb6340', '#ffd600', '#f56565'];
                    
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: this.agentStats.map(agent => agent.name),
                            datasets: [{
                                data: this.agentStats.map(agent => agent.conversations),
                                backgroundColor: colors.slice(0, this.agentStats.length),
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
                                        color: '#fff',
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                }
                            },
                            cutout: '60%'
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
