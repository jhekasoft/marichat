var ShoppingList = {
    webSocketsUlr: '',
    addUrl: '',
    getEditFormUrl: '',
    editUrl: '',
    deleteUrl: '',
    getListUrl: '',
    changeStatusUrl: '',
    isEditingRowNow: false, // current user edit items
    webSocketConnection: null,
    webSocketConnectionIsOpened: false,
    notifySection: 'shopping-list',
    notifyMessageBeginEditing: 'beginEditing',
    notifyMessageCancelEditing: 'cancelEditing',
    notifyMessageAdded: 'added',
    notifyMessageUpdated: 'updated',
    notifyMessageDeleted: 'deleted',
    notifyMessageStatusChanged: 'statusChanged',
    connectionCheckTimeout: null,
    checkConnectionTime: 10000,
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

            // Send notification for other users
            ShoppingList.sendNotification({
                message: ShoppingList.notifyMessageAdded
            });
        });
    },
    getEditForm: function(id) {
        ShoppingList.isEditingRowNow = true;

        ShoppingList.showRowLoadingSpiner(id);

        $.ajax({
            type: "POST",
            url: ShoppingList.getEditFormUrl,
            data: {'id': id}
        }).done(function(response) {
            ShoppingList.hideRowLoadingSpiner(id);

            if ('ok' == response.result && response.editFormHtml) {
                ShoppingList.cancelAllEdit();

                $('.shopping-list-row-form-' + id).html(response.editFormHtml);
                $('.shopping-list-row-form-' + id).show();
                $('.shopping-list-row-data-' + id).hide();
                $('.shopping-list-actions-default-' + id).hide();
                $('.shopping-list-actions-edit-' + id).show();

                // Send notification for other users
                ShoppingList.sendNotification({
                    message: ShoppingList.notifyMessageBeginEditing, id: id
                });
            }

            if (response.errorDetails) {
                alert(response.errorDetails);
            }
        });
    },
    showRowLoadingSpiner: function(id) {
        $('.shopping-list-row-loading-' + id).css('display', 'inline-block');
    },
    hideRowLoadingSpiner: function(id) {
        $('.shopping-list-row-loading-' + id).hide();
    },
    cancelEdit: function(id) {
        $('.shopping-list-row-form-' + id).hide();
        $('.shopping-list-row-data-' + id).show();
        $('.shopping-list-actions-default-' + id).show();
        $('.shopping-list-actions-edit-' + id).hide();

        ShoppingList.isEditingRowNow = false;

        // Send notification for other users
        ShoppingList.sendNotification({
            message: ShoppingList.notifyMessageCancelEditing, id: id
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

                ShoppingList.isEditingRowNow = false;

                // Send notification for other users
                ShoppingList.sendNotification({
                    message: ShoppingList.notifyMessageUpdated, id: id
                });
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

            // Send notification for other users
            ShoppingList.sendNotification({
                message: ShoppingList.notifyMessageDeleted, id: id
            });
        });
    },
    refreshList: function() {
        $.ajax({
            type: "POST",
            url: ShoppingList.getListUrl,
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.shopping-list-items').html(response.listHtml);
            }
        });
    },
    refreshListIfNotEditing: function() {
        if (this.isEditingRowNow) {
            return false;
        }

        this.refreshList();
    },
    changeStatus: function(id, status) {
        $.ajax({
            type: "POST",
            url: ShoppingList.changeStatusUrl,
            data: {id: id, status: status}
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.shopping-list-items').html(response.listHtml);

                // Send notification for other users
                ShoppingList.sendNotification({
                    message: ShoppingList.notifyMessageStatusChanged, id: id
                });
            }
        });
    },
    createSocketConnection: function() {
        this.webSocketConnection = new WebSocket(this.webSocketsUlr);

        this.webSocketConnection.onopen = function(e) {
            ShoppingList.onlineStatusOn();
        };

        this.webSocketConnection.onclose = function(e) {
            ShoppingList.onlineStatusOff();

            ShoppingList.connectionCheckTimeout = setTimeout(function() {
                ShoppingList.checkSocketConnection();
            }, ShoppingList.checkConnectionTime);
        };

        this.webSocketConnection.onmessage = function(e) {
            ShoppingList.handleNotification(e.data);
        };
    },
    isOnline: function() {
        return ShoppingList.webSocketConnectionIsOpened;
    },
    onlineStatusOn: function() {
        ShoppingList.webSocketConnectionIsOpened = true;

        $('.online-status-online').show();
        $('.online-status-offline').hide();
    },
    onlineStatusOff: function() {
        ShoppingList.webSocketConnection = null;
        ShoppingList.webSocketConnectionIsOpened = false;

        $('.online-status-online').hide();
        $('.online-status-offline').show();
    },
    checkSocketConnection: function() {
        if (this.isOnline()) {
            clearTimeout(this.connectionCheckTimeout)
        } else if(!ShoppingList.webSocketConnection) {
            this.createSocketConnection();

            if (this.connectionCheckTimeout) {
                return;
            }
        }
    },
    sendNotification: function(messageJson) {
        if (!this.webSocketConnectionIsOpened) {
            return false;
        }

        messageJson.section = this.notifySection;
        this.webSocketConnection.send($.toJSON(messageJson));
    },
    handleNotification: function(message) {
        messageJson = $.evalJSON(message);

        if (messageJson.section != this.notifySection) {
            return false;
        }

        if (messageJson.message == ShoppingList.notifyMessageBeginEditing) {
            $('.shopping-list-row-editing-' + messageJson.id).show();
        }

        if (messageJson.message == ShoppingList.notifyMessageCancelEditing) {
            $('.shopping-list-row-editing-' + messageJson.id).hide();
        }

        if (messageJson.message == ShoppingList.notifyMessageUpdated) {
            $('.shopping-list-row-editing-' + messageJson.id).hide();
            this.refreshListIfNotEditing();
        }

        if (messageJson.message == ShoppingList.notifyMessageAdded ||
                messageJson.message == ShoppingList.notifyMessageDeleted ||
                messageJson.message == ShoppingList.notifyMessageStatusChanged) {
            this.refreshListIfNotEditing();
        }
    }
};
