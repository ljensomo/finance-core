<template>
    <div class="container-fluid">
        <div class="card shadow-sm border border-secondary-subtle rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-file-alt"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Import Logs</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive rounded-3 overflow-hidden border border-secondary-subtle">
                    <BTable
                        :items="logs"
                        :fields="fields"
                        :per-page="perPage"
                        :current-page="currentPage"
                        striped
                        hover
                        small
                        show-empty
                    >
                    </BTable>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { ref } from 'vue';

    export default {
        data() {
            return {
                module: 'import-log',
                utilityUrl: '/api/import-logs',
                logs: [],
                fields: [
                    { key: 'id', label: 'ID', sortable: true },
                    { key: 'rows_imported', label: 'Rows Imported', sortable: true },
                    { key: 'rows_failed', label: 'Rows Failed', sortable: true },
                    { key: 'total_rows', label: 'Total Rows', sortable: true },
                    { key: 'last_row_number', label: 'Last Row Number', sortable: true },
                    { key: 'formatted_created_at', label: 'Date Imported', sortable: true },
                ],
                // datatable
                perPage: ref(20),
                currentPage: ref(1),
                rows: ref(0),
            }
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.perPage.value;
                const end = start + this.perPage.value;
                return this.budgets.slice(start, end);
            },
            startRow() {
                return this.budgets.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
            },
            endRow() {
                return Math.min(this.currentPage * this.perPage, this.budgets.length)
            },
        },
        methods: {
            async loadLogs(){
                this.logs = await this.fetchRecords({ url: this.utilityUrl });
            }
        },
        mounted(){
            this.loadLogs();
        }
    }
</script>
