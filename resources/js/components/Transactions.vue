<template>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary-subtle text-primary me-3 px-3 py-2 rounded">
                        <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Transactions</h5>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-4 align-items-center">
                        <div class="col-md-8 d-flex gap-2">
                            <button class="btn btn-sm btn-primary shadow-sm" @click="add('transactionModal')"><i class="fa-solid fa-plus me-2"></i>Add Transaction</button>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" disabled data-bs-target="#importModal"><i class="fa-solid fa-file-import me-2"></i>Import Transactions</button>
                            <button class="btn btn-sm btn-info" @click="syncTransactions"><i class="fa-solid fa-sync me-2"></i>Sync Google Sheet Transactions</button>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                </span>
                                <BFormInput
                                    v-model="filter"
                                    placeholder="Type to Search..."
                                    size="sm"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <BTable
                            :items="transactions"
                            :fields="fields"
                            :per-page="perPage"
                            :current-page="currentPage"
                            :filter="filter"
                            striped
                            hover
                            class="align-middle border-top"
                            thead-class="table-light text-uppercase small fw-bold"
                        >
                            <template #cell(type)="row">
                                <span v-if="row.item.type == 1" class="badge rounded-pill bg-success-subtle text-success px-3">
                                <i class="fa-solid fa-circle-arrow-down me-1"></i> Income
                                </span>
                                <span v-else class="badge rounded-pill bg-danger-subtle text-danger px-3">
                                <i class="fa-solid fa-circle-arrow-up me-1"></i> Expense
                                </span>
                            </template>

                            <template #cell(category.name)="row">
                                <div class="d-flex align-items-center">
                                    <div class="category-icon-sm me-2 d-flex align-items-center justify-content-center rounded-circle"
                                        :class="getCategoryStyle(row.item.category.name).colorClass"
                                    >
                                    <i :class="getCategoryStyle(row.item.category.name).icon"></i>
                                    </div>
                                    <span class="fw-medium text-secondary">{{ row.item.category.name }}</span>
                                </div>
                            </template>

                            <template #cell(amount)="row">
                                <span :class="row.item.type == 1 ? 'text-success' : 'text-danger'" class="fw-bold">
                                {{ formatPeso(row.item.amount) }}
                                </span>
                            </template>

                            <template #cell(actions)="row">
                                <div class="d-flex gap-1">
                                <BButton size="sm" variant="light" class="text-warning border" @click="fetchTransaction(row.item.id)">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </BButton>
                                <BButton size="sm" variant="light" class="text-danger border" @click="deleteTransaction(row.item.id)">
                                    <i class="fa-solid fa-trash"></i>
                                </BButton>
                                </div>
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
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold" id="transactionModalLabel">
            <i class="fa-solid fa-money-bill-transfer me-2 text-primary"></i>
            {{ form.id ? 'Edit' : 'New' }} Transaction
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form @submit.prevent="submitForm" novalidate>
          <div class="modal-body p-4">
            
            <div class="mb-4 text-center">
              <div class="btn-group w-100" role="group" aria-label="Transaction Type">
                <input type="radio" class="btn-check" name="type" id="expense" value="2" v-model="form.type" @change="loadCategories" required>
                <label class="btn btn-outline-danger py-2" for="expense">
                  <i class="fa-solid fa-arrow-circle-up me-1"></i> Expense
                </label>

                <input type="radio" class="btn-check" name="type" id="income" value="1" v-model="form.type" @change="loadCategories">
                <label class="btn btn-outline-success py-2" for="income">
                  <i class="fa-solid fa-arrow-circle-down me-1"></i> Income
                </label>
              </div>
            </div>

            <div class="mb-4">
              <label for="amount" class="form-label small text-uppercase fw-bold text-muted">Amount</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0 text-muted">₱</span>
                <input 
                  type="number" 
                  class="form-control border-start-0 ps-0 fw-bold" 
                  :class="form.type == '1' ? 'text-success' : 'text-danger'"
                  id="amount" 
                  step="0.01" 
                  placeholder="0.00" 
                  v-model="form.amount" 
                  required
                >
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label small text-uppercase fw-bold text-muted">Description</label>
              <input type="text" class="form-control bg-light border-0" id="description" placeholder="What was this for?" v-model="form.description" required>
            </div>

            <div class="row gx-3">
              <div class="col-md-6 mb-3">
                <label for="date" class="form-label small text-uppercase fw-bold text-muted">Date</label>
                <input type="date" class="form-control bg-light border-0" id="date" v-model="form.date" required>
              </div>

              <div class="col-md-6 mb-3">
                <label for="category" class="form-label small text-uppercase fw-bold text-muted">Category</label>
                <select class="form-select bg-light border-0" id="category" v-model="form.category_id" required>
                  <option selected disabled value="">Choose...</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="modal-footer border-0 p-4 pt-0">
            <button type="button" class="btn btn-link text-muted text-decoration-none px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-pill">
              Save Transaction
            </button>
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
            },
            getCategoryStyle(category) {
                const name = category?.toLowerCase() || '';
                
                const styles = {
                food:           { icon: 'fa-solid fa-utensils',    colorClass: 'bg-orange-subtle text-orange' },
                transportation: { icon: 'fa-solid fa-car',         colorClass: 'bg-blue-subtle text-blue' },
                personal:       { icon: 'fa-solid fa-user',        colorClass: 'bg-purple-subtle text-purple' },
                debt:           { icon: 'fa-solid fa-credit-card', colorClass: 'bg-danger-subtle text-danger' },
                insurance:      { icon: 'fa-solid fa-shield-heart',colorClass: 'bg-info-subtle text-info' },
                utilities:      { icon: 'fa-solid fa-bolt',        colorClass: 'bg-warning-subtle text-warning' },
                housing:        { icon: 'fa-solid fa-house',       colorClass: 'bg-indigo-subtle text-indigo' },
                savings:        { icon: 'fa-solid fa-piggy-bank',  colorClass: 'bg-success-subtle text-success' },
                miscellaneous:  { icon: 'fa-solid fa-box',         colorClass: 'bg-secondary-subtle text-secondary' },
                };

                return styles[name] || { icon: 'fa-solid fa-circle', colorClass: 'bg-light text-muted' };
            }
        }
    }
</script>

<style scoped>
.category-icon-sm {
  width: 28px;
  height: 28px;
  font-size: 0.75rem;
}

/* Custom Subtles (if not in your Bootstrap version) */
.bg-orange-subtle { background-color: #fff3e0; }
.text-orange { color: #ef6c00; }

.bg-purple-subtle { background-color: #f3e5f5; }
.text-purple { color: #7b1fa2; }

.bg-blue-subtle { background-color: #e3f2fd; }
.text-blue { color: #1976d2; }

.bg-indigo-subtle { background-color: #e8eaf6; }
.text-indigo { color: #3f51b5; }

/* Ensure icons are centered */
.category-icon-sm i {
  display: block;
}
</style>