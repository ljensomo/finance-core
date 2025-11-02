<template>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><i class="fa-solid fa-file-invoice me-2"></i>Transactions</div>

                <div class="card-body">
                    <button class="btn btn-primary" @click="add('transactionModal')"><i class="fa-solid fa-plus me-2"></i>Add Transaction</button>&nbsp;
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fa-solid fa-file-import me-2"></i>Import Transactions</button>
                    <button class="btn btn-info" @click="syncTransactions"><i class="fa-solid fa-sync me-2"></i>Sync Google Sheet Transactions</button>
                    <hr>
                    <div class="container-fluid table-responsive my-2">
                        <BFormInput
                            v-model="filter"
                            placeholder="Type to Search..."
                            class="mb-3"
                            size="md"
                            style="width:400px;"
                        />
                        <BTable
                            :items="transactions"
                            :fields="fields"
                            :per-page="perPage"
                            :current-page="currentPage"
                            :filter="filter"
                            striped
                            hover
                            bordered
                        >
                            <template #cell(type)="row">
                                <span v-if="row.item.type == 1" class="badge bg-success text-white">
                                    <i class="fa-solid fa-arrow-up me-2"></i>Income
                                </span>
                                <span v-else class="badge bg-danger text-white">
                                    <i class="fa-solid fa-arrow-down me-2"></i>Expense
                                </span>
                            </template>
                            <template #cell(amount)="row">
                                {{ this.formatPeso(row.item.amount) }}
                            </template>
                            <template #cell(actions)="row">
                                <BButton size="sm" variant="warning" @click="fetchTransaction(row.item.id)">Edit</BButton>&nbsp;
                                <BButton size="sm" variant="danger" @click="deleteTransaction(row.item.id)">Delete</BButton>
                            </template>
                        </BTable>
                        <div class="d-flex justify-content-between align-items-center mb-2 py-3">
                            <p>
                                Showing {{ startRow }}–{{ endRow }} of {{ transactions.length }} rows
                            </p>
                            <BPagination
                                v-model="currentPage"
                                :total-rows="transactions.length"
                                :per-page="perPage"
                                align="end"
                                first-text="⏮"
                                prev-text="Prev"
                                next-text="Next"
                                last-text="⏭"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- transaction modals -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transactionModalLabel">Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="submitForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="expense" value="2" v-model="form.type" @change="loadCategories" requied>
                                <label class="form-check-label" for="expense">Expense</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="income" value="1" v-model="form.type" @change="loadCategories" required>
                                <label class="form-check-label" for="income">Income</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" v-model="form.date" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Description" v-model="form.description" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" placeholder="₱0.00" v-model="form.amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" v-model="form.category_id" required>
                            <option selected disabled value="">Choose...</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- import modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="submitFile">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-control" type="file" name="file" id="file" accept=".csv, text/csv" @change="onFileChange" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import Transaction(s)</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <LoadingModal :visible="isLoading"></LoadingModal>
</template>

<script>
    import LoadingModal from './Shared/LoadingModal.vue';
    import { ref } from 'vue'

    export default {
        components: {
            LoadingModal
        },
        mounted() {
            this.fetchTransactions();
        },
        data() {
            return {
                isLoading: false,
                fields: [
                    { key: 'type', label: 'Type', sortable: true },
                    { key: 'date', label: 'Date', sortable: true },
                    { key: 'description', label: 'Description', sortable: true },
                    { key: 'amount', label: 'Amount', sortable: true, class: "text-end" },
                    { key: 'category.name', label: 'Category', sortable: true },
                    { key: 'actions', label: 'Actions' }
                ],
                perPage: ref(10),
                currentPage: ref(1),
                rows: ref(0),
                filter: ref(''),
                transactions: [],
                categories: [],
                form: {
                    id: null,
                    type: null,
                    date: null,
                    description: '',
                    amount: null,
                    category_id: null,
                },
                isEditing: false,
                file: null
            };
        },
        computed: {
            paginatedItems() {
                const start = (this.currentPage - 1) * this.perPage.value;
                const end = start + this.perPage.value;
                return this.transactions.slice(start, end);
            },
            startRow() {
                return this.transactions.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
            },
            endRow() {
                return Math.min(this.currentPage * this.perPage, this.transactions.length)
            }
        },
        methods: {
            fetchTransactions() {
                axios.get('/api/transactions').then(response => {
                    this.transactions = response.data;
                    this.rows = this.transactions.length;
                }).catch(error => {
                    console.error('Error fetching transactions:', error);
                });
            },
            add(modalId) {
                this.isEditing = false;
                this.form = {
                        id: null,
                        type: null,
                        date: null,
                        description: '',
                        amount: null,
                        category_id: null,
                    };
                this.openModal(modalId);
            },
            submitForm(e) {
                e.preventDefault();

                if(this.isEditing){
                    this.updateItem({
                        url: `/api/transactions/${this.form.id}`,
                        data: this.form,
                        successMessage: 'Transaction updated successfully!',
                        errorMessage: 'Failed to update transaction.',
                        callback: () => {
                            e.target.reset();
                            this.editing = false;
                            this.fetchTransactions();
                            this.closeModal('transactionModal');
                        }
                    });
                }else{
                    this.addItem({
                        url: '/api/transactions',
                        data: this.form,
                        successMessage: 'Transaction added successfully!',
                        errorMessage: 'Failed to add transaction.',
                        callback: () => {
                            e.target.reset();
                            this.fetchTransactions();
                            this.closeModal('transactionModal');
                        }
                    });
                }
            },
            fetchTransaction(id) {
                this.fetchItem({
                    url: `/api/transactions/${id}`,
                    successMessage: 'Transaction fetched successfully!',
                    errorMessage: 'Failed to fetch transaction.',
                    callback: (response) => {
                        this.form = response;
                        this.loadCategories();
                        this.isEditing = true;
                        this.openModal('transactionModal');
                    }
                });
            },
            deleteTransaction(id) {
                this.deleteItem({
                    url: `/api/transactions/${id}`,
                    successMessage: 'Transaction deleted successfully!',
                    errorMessage: 'Failed to delete transaction.',
                    callback: () => {
                        this.fetchTransactions();
                    }
                });
            },
            onFileChange(e) {
                this.file = e.target.files[0];
            },
            submitFile(){
                const formData = new FormData();
                formData.append('file', this.file);

                axios.post('/api/transactions/import', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.fetchTransactions();
                    this.closeModal('importModal');
                }).catch(error => {
                    this.$swal('Error!', 'Failed to import transactions.', 'error');
                    console.error('Error importing transactions:', error);
                });
            },
            loadCategories() {
                this.categories = [];
                this.fetchCategories(this.form.type);
            },
            async syncTransactions() {
                this.isLoading = true;
                try{
                    const response = await axios.get('/google-sheet/sync');
                    this.$swal({
                        title: 'Sync Completed!',
                        html: `
                            Rows Imported: ${response.data.rows_imported}<br/>
                            Rows Failed: ${response.data.rows_failed}<br/>
                            Rows Total: ${response.data.total_rows}<br/>
                        `,
                        icon: 'success',
                    });
                }catch(error){
                    this.$swal('Error!', 'Failed to sync transactions.', 'error');
                    console.error('Error syncing transactions:', error);
                }finally{
                    this.isLoading = false;
                    this.fetchTransactions();
                }
            }
        }
    }
</script>
