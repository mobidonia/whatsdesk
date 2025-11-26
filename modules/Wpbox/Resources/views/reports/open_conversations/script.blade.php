@hasrole('owner')
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Vue !== 'undefined' && document.getElementById('openConversationsReport')) {
        new Vue({
            el: '#openConversationsReport',
            data: {
                stats: {
                    open: 0,
                    unattended: 0,
                    unassigned: 0,
                    pending: 0,
                    total: 0,
                    openPercentage: 0,
                    unattendedPercentage: 0,
                    unassignedPercentage: 0,
                    pendingPercentage: 0
                },
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
                    
                    axios.get('/api/wpbox/reports/open-conversations')
                        .then(response => {
                            if (response.data.status === 'success') {
                                this.stats = response.data.data;
                            } else {
                                this.error = response.data.message || 'Unknown error';
                            }
                        })
                        .catch(error => {
                            console.error('Error loading open conversations report:', error);
                            this.error = error.response?.data?.message || 'Failed to load report data';
                        })
                        .finally(() => {
                            this.loading = false;
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