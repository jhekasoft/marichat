var ShoppingList = {
    addUrl: '',
    getEditFormUrl: '',
    editUrl: '',
    cancelEditUrl: '',
    deleteUrl: '',
    add: function() {
        $.ajax({
            type: "POST",
            url: ShoppingList.addUrl,
            data: $('.shopping-list-add-form input').serialize()
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.shopping-list-items').html(response.listHtml);
            }
            
            if (response.addFormHtml) {
                $('.shopping-list-add-form').html(response.addFormHtml);
            }
        });
    },
    getEditForm: function(id) {        
        $.ajax({
            type: "POST",
            url: ShoppingList.getEditFormUrl,
            data: {'id': id}
        }).done(function(response) {
            if ('ok' == response.result && response.editFormHtml) {
                ShoppingList.cancelAllEdit();
                
                $('.shopping-list-row-form-' + id).html(response.editFormHtml);
                $('.shopping-list-row-form-' + id).show();
                $('.shopping-list-row-data-' + id).hide();
                $('.shopping-list-actions-default-' + id).hide();
                $('.shopping-list-actions-edit-' + id).show();
            }
            
            if (response.errorDetails) {
                alert(response.errorDetails);
            }
        });
    },
    cancelEdit: function(id) {
        $('.shopping-list-row-form-' + id).hide();
        $('.shopping-list-row-data-' + id).show();
        $('.shopping-list-actions-default-' + id).show();
        $('.shopping-list-actions-edit-' + id).hide();
        
        $.ajax({
            type: "POST",
            url: ShoppingList.cancelEditUrl,
            data: {'id': id}
        }).done(function(response) {
        });
    },
    cancelAllEdit: function() {
        $('.shopping-list-row-form').hide();
        $('.shopping-list-row-data').show();
        $('.shopping-list-actions-default').show();
        $('.shopping-list-actions-edit').hide();
    },
    edit: function(id) {
        $.ajax({
            type: "POST",
            url: ShoppingList.editUrl,
            data: $('.shopping-list-row-form-' + id + ' input').serialize()
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.shopping-list-items').html(response.listHtml);
            }
        });
    },
    delete: function(id) {
        if (!confirm("Are you sure you want to delete?")) {
            return false;
        }
        
        $.ajax({
            type: "POST",
            url: ShoppingList.deleteUrl,
            data: {'id': id}
        }).done(function(response) {
            $('.shopping-list-items').html(response.html);
        });
    }
};
