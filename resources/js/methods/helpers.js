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
        deleteItem(parameters){
            axios.delete(parameters.url).then(response => {
                if(response.status == 200) {
                    this.$swal({
                        title: 'Deleted!',
                        text: 'Successfully deleted record!',
                        icon: 'success',
                    }).then(() => {
                        parameters.callback();
                    });
                }else{
                    this.$swal({
                        title: 'Error!',
                        text: 'Failed to delete record!',
                        icon: 'error',
                    });
                }
            }).catch(error => {
                this.$swal('Error!', parameters.errorMessage, 'error');
                console.error("error deleting item", error);
            });
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
        submitForm(e, parameters = null){
            e.preventDefault();
            
            if(this.isEditing){
                this.updateItem({
                    url: parameters.updateUrl,
                    data: parameters.form,
                    successMessage: parameters.title+' updated successfully!',
                    errorMessage: 'Failed to update '+parameters.title,
                    callback: () => {
                        e.target.reset();
                        this.editing = false;
                        if(parameters.callback != null){
                            parameters.callback();
                        }
                        this.closeModal(parameters.modalId);
                    }
                });
            }else{
                this.addItem({
                    url: parameters.addUrl,
                    data: parameters.form,
                    successMessage: parameters.title+' added successfully!',
                    errorMessage: 'Failed to add '+parameters.title,
                    callback: () => {
                        e.target.reset();
                        if(parameters.callback != null){
                            parameters.callback();
                        }
                        this.closeModal(parameters.modalId);
                    }
                })
            }
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