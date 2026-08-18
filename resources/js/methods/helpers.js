export const Helpers = {
    methods: {
        async fetchRecords(parameters){
            try {
                const response = await axios.get(parameters.url)
                return response.data
            } catch (error) {
                console.error('Error fetching records:', error)
                return [] // or null, depending on your fallback
            }
        },
        async deleteItem({
            url,
            successMessage = 'Record deleted successfully!',
            errorMessage = 'Failed to delete record. Please try again.',
            confirmTitle = 'Are you sure?',
            confirmText = 'You won\'t be able to revert this!',
            callback
        }) {
            // Confirmation dialog before proceeding with deletion
            const confirmation = await this.$swal({
                title: confirmTitle,
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });

            if (!confirmation.isConfirmed) return;

            try {
                const response = await axios.delete(url);

                if (response.status >= 200 && response.status < 300) {
                    // Interactive SweetAlert Toast
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Deleted!',
                        text: successMessage,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        showCloseButton: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', this.$swal.stopTimer);
                            toast.addEventListener('mouseleave', this.$swal.resumeTimer);
                        }
                    });

                    if (typeof callback === 'function') {
                        callback(response.data);
                    }
                }
            } catch (error) {
                console.error('Error during DELETE request:', error);

                const detailedError = error.response?.data?.message || errorMessage;

                // Interactive Error Alert with dynamic retry
                this.$swal({
                    icon: 'error',
                    title: 'Action Failed',
                    text: detailedError,
                    showCancelButton: true,
                    confirmButtonText: 'Try Again',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Re-trigger the deletion request on retry
                        this.deleteItem({ url, successMessage, errorMessage, confirmTitle, confirmText, callback });
                    }
                });
            }
        },
        async fetchItem(parameters){
            try {
                const response = await axios.get(parameters.url)
                if(parameters.callback != null){
                    parameters.callback(response.data);
                }
                return response.data
            } catch (error) {
                this.$swal('Error!', 'Failed to fetch item.', 'error');
                return [] // or null, depending on your fallback
            }
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
        async addItem({ 
            url, 
            data, 
            successMessage = 'Record added successfully!', 
            errorMessage = 'Something went wrong. Please try again.', 
            callback 
        }) {
            try {
                const response = await axios.post(url, data);

                // Accept 200 OK or 201 Created
                if (response.status >= 200 && response.status < 300) {
                    // Non-blocking interactive SweetAlert Toast
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Saved!',
                        text: successMessage,
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        showCloseButton: true,
                        didOpen: (toast) => {
                            // Pause timer on hover so user has time to read
                            toast.addEventListener('mouseenter', this.$swal.stopTimer);
                            toast.addEventListener('mouseleave', this.$swal.resumeTimer);
                        }
                    });

                    // Execute callback immediately
                    if (typeof callback === 'function') {
                        callback(response.data);
                    }
                }
            } catch (error) {
                console.error("Error adding item:", error);

                // Fall back to server message if available (e.g. 422 validation errors)
                const detailedError = error.response?.data?.message || errorMessage;

                // Interactive Error Alert with retry action
                this.$swal({
                    icon: 'error',
                    title: 'Action Failed',
                    text: detailedError,
                    showCancelButton: true,
                    confirmButtonText: 'Try Again',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.addItem({ url, data, successMessage, errorMessage, callback });
                    }
                });
            }
        },
        async saveItem({
                url,
                data,
                method = 'post', // 'post', 'put', or 'patch'
                successMessage,
                errorMessage = 'Something went wrong. Please try again.',
                callback
            }) {
                const httpMethod = method.toLowerCase();
                const isPost = httpMethod === 'post';

                // Default messages based on action
                const defaultSuccess = isPost ? 'Record created successfully!' : 'Record updated successfully!';
                const toastTitle = isPost ? 'Created!' : 'Updated!';

                try {
                    // Dynamically invoke axios method (axios.post, axios.put, etc.)
                    const response = await axios[httpMethod](url, data);

                    if (response.status >= 200 && response.status < 300) {
                        // Interactive SweetAlert Toast
                        this.$swal({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: toastTitle,
                            text: successMessage || defaultSuccess,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            showCloseButton: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', this.$swal.stopTimer);
                                toast.addEventListener('mouseleave', this.$swal.resumeTimer);
                            }
                        });

                        if (typeof callback === 'function') {
                            callback(response.data);
                        }
                    }
                } catch (error) {
                    console.error(`Error during ${httpMethod.toUpperCase()} request:`, error);

                    const detailedError = error.response?.data?.message || errorMessage;

                    // Interactive Error Alert with dynamic retry
                    this.$swal({
                        icon: 'error',
                        title: 'Action Failed',
                        text: detailedError,
                        showCancelButton: true,
                        confirmButtonText: 'Try Again',
                        cancelButtonText: 'Close',
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Re-trigger the exact same dynamic request on retry
                            this.saveItem({ url, data, method, successMessage, errorMessage, callback });
                        }
                    });
                }
        },
        fetchCategories(type = null){
            try{
                axios.get('/api/categories', {
                    params: {
                        type: type
                    }
                }).then(response => {
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
        },
        triggerAddModal(modalId, callback = null){
            this.isEditing = false;
            this.openModal(modalId);
            if(callback != null){
                callback();
            }
        },
        submitForm(e, parameters = {}) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }

            // Safe destructuring with fallback values
            const {
                form = {},
                updateUrl,
                addUrl,
                title = 'Record',
                modalId = null,
                callback
            } = parameters || {};

            // Dynamic resolution based on edit state
            const isEditing = Boolean(this.isEditing || this.editing || this.isEditMode);
            const action = isEditing ? 'updated' : 'added';
            const verb = isEditing ? 'update' : 'add';
            const method = isEditing ? 'put' : 'post';
            const url = isEditing ? updateUrl : addUrl;

            this.saveItem({
                url,
                data: form,
                method,
                successMessage: `${title} ${action} successfully!`,
                errorMessage: `Failed to ${verb} ${title}.`,
                callback: (responseData) => {
                    // Safely reset native DOM form if available
                    if (e?.target && typeof e.target.reset === 'function') {
                        e.target.reset();
                    }

                    // Synchronize reactive edit state flags
                    this.isEditing = false;
                    this.editing = false;

                    // Execute component callback
                    if (typeof callback === 'function') {
                        callback(responseData);
                    }

                    // Conditionally close modal if modalId is supplied
                    if (modalId && typeof this.closeModal === 'function') {
                        this.closeModal(modalId);
                    }
                }
            });
        },
        capitalizeFirstLetter(text) {
            return text.charAt(0).toUpperCase() + text.slice(1);
        },
        setFormVariables(formFields){
            return formFields.filter(obj => 'key' in obj).reduce((acc, obj) => {
                acc[obj.key] = '';
                return acc;
            }, {});
        },
        isEmptyObject(obj) {
            return obj && typeof obj === 'object' && !Array.isArray(obj) && Object.keys(obj).length === 0
        },
        buildOptions(data, label, value){
            let options = [];
            for(let index in data){
                options.push({
                    value: data[index][value],
                    label: data[index][label]
                });
            }
            return options;
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