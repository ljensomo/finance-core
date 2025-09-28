<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Transactions</div>

                <div class="card-body">
                    <button class="btn btn-primary mb-3" @click="add('transactionModal')">Add Transaction</button>&nbsp;
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#importModal">Import Transactions</button>
                    <table class="table table-bordered table-striped" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="transaction in transactions" :key="transaction.id">
                                <td>{{ transaction.type == 1 ? 'Income' : 'Expense' }}</td>
                                <td>{{ transaction.date }}</td>
                                <td>{{ transaction.description }}</td>
                                <td class="text-end">{{ formatPeso(transaction.amount) }}</td>
                                <td>{{ transaction.category ? transaction.category.name : '(Uncategorized)' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" @click="fetchTransaction(transaction.id)">Edit</button>&nbsp;
                                    <button class="btn btn-sm btn-danger" @click="deleteTransaction(transaction.id)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                <input class="form-check-input" type="radio" name="type" id="expense" value="2" v-model="form.type" requied>
                                <label class="form-check-label" for="expense">Expense</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="income" value="1" v-model="form.type" required>
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
</template>

<script>
    import { Helpers } from '../methods/helpers.js';
    export default {
        mixins: [Helpers],
        mounted() {
            this.fetchTransactions();
            this.fetchCategories();
        },
        data() {
            return {
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
        methods: {
            fetchTransactions() {
                axios.get('/api/transactions').then(response => {
                    this.transactions = response.data;
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
            }
        }
    }
</script>
