<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Current Balance</div>

                    <div class="card-body">
                        <p>Your current balance is:</p>
                        <h2>₱0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Montly Income vs. Expenses Summary</div>

                    <div class="card-body">
                        <p>Your monthly income is:</p>
                        <h2>₱0.00</h2>
                        <p>Your monthly expenses are:</p>
                        <h2>₱0.00</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row py-4 justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Recent Transactions</div>

                    <div class="card-body">
                        <ul>
                            <li>No recent transactions.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Spending Categories</div>

                    <div class="card-body">
                        <ul>
                            <li>No spending categories.</li>
                        </ul>
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
