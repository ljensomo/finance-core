<template>
    <div class="modal" id="loading-modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center d-flex align-items-center justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2">Syncing Google Sheets...</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            visible: Boolean
        },
        watch: {
            visible: {
                immediate: true,
                handler(value) {
                    if (value) {
                        this.openModal("loading-modal");
                    }else{
                        if(document.getElementById("loading-modal")){ // close only if the modal exists
                            this.closeModal("loading-modal");
                        }
                    }
                }
            }
        }
    }
</script>
