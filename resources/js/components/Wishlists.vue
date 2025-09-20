<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Wishlist</div>

                    <div class="card-body">
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addWishlistModal">Add Wishlist</button>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>
                                    <th>Estimated Cost</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Target Date</th>
                                    <th>Remarks</th>
                                    <th>Date Added</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>New Laptop</td>
                                    <td>₱50,000.00</td>
                                    <td>High</td>
                                    <td>Pending</td>
                                    <td>2023-12-31</td>
                                    <td>For work and personal use</td>
                                    <td>2023-01-15</td>
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
