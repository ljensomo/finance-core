export const Helpers = {
    methods: {
        deleteItem(parameters){
            axios.delete(parameters.url).then(response => {
                if(response.status == 200) {
                    this.$swal({
                        title: 'Deleted!',
                        text: parameters.successMessage,
                        icon: 'success',
                    }).then(() => {
                        parameters.callback();
                    });
                }else{
                    this.$swal({
                        title: 'Error!',
                        text: parameters.errorMessage,
                        icon: 'error',
                    });
                }
            }).catch(error => {
                this.$swal('Error!', parameters.errorMessage, 'error');
                console.error("error deleting item", error);
            });
        },
        fetchItem(parameters){
            axios.get(parameters.url).then(response => {
                if(response.status == 200) {
                    parameters.callback(response.data);
                }else{
                    this.$swal('Error!', parameters.errorMessage, 'error');
                }
            }).catch(error => {
                this.$swal('Error!', parameters.errorMessage, 'error');
                console.error("error fetching item", error);
            });
        },
        updateItem(parameters){
            axios.put(parameters.url, parameters.data)
            .then(response => {
                if(response.status == 200) {
                    this.$swal({
                        title: 'Done!',
                        text: parameters.successMessage,
                        icon: 'success',
                    }).then(() => {
                        parameters.callback();
                    });
                }else{
                    this.$swal({
                        title: 'Error!',
                        text: parameters.errorMessage,
                        icon: 'error',
                    });
                }
            });
        },
        addItem(parameters){
            axios.post(parameters.url, parameters.data)
            .then(response => {
                if(response.status == 200) {
                    this.$swal({
                        title: 'Done!',
                        text: parameters.successMessage,
                        icon: 'success',
                    }).then(() => {
                        parameters.callback();
                    });
                }else{
                    this.$swal( 'Error!', parameters.errorMessage, 'error');
                }
            }).catch(error => {
                this.$swal('Error!', parameters.errorMessage, 'error');
                console.error("error adding item", error);
            });
        },
        fetchCategories(){
            try{
                axios.get('/api/categories').then(response => {
                    this.categories = response.data;
                }).catch(error => {
                    console.error('Error fetching categories:', error);
                });
            }catch(error){
                console.error('Error fetching categories:', error);
            }
        },
        formatPeso(value) {
            if (isNaN(value) || value === null) {
                return '';
            }
            
            // Create an Intl.NumberFormat instance for the Philippine Peso
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                minimumFractionDigits: 2,
            }).format(value);
        },
        closeModal(modalId) {
            const modalElement = document.getElementById(modalId);
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();
        },
        openModal(modalId) {
            const modalElement = document.getElementById(modalId);
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        }
    }
}