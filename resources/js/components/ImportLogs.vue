<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-file-alt me-2"></i>Import Logs</div>
                    <div class="card-body">
                        <DataTable
                            :items="logs"
                            :fields="fields"
                            :utilityUrl="utilityUrl"
                            :module="module"
                            :formatters="formatters"
                        >
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import DataTable from './Shared/DataTable.vue';
    export default {
        components: { DataTable },
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
                    { key: 'created_at', label: 'Imported At', sortable: true },
                ],
                formatters:{
                    created_at: (value) => {
                        return new Date(value).toLocaleString();
                    }
                }
            }
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
