<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Transactions</div>

                    <div class="card-body">
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTransactionModal">Add Transaction</button>&nbsp;
                        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#exportTransactionsModal">Export Transactions</button>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2023-01-01</td>
                                    <td>Income</td>
                                    <td>₱1000.00</td>
                                    <td>Salary</td>
                                    <td>Monthly salary payment</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">Edit</button>&nbsp;
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            
        }
    }
</script>
