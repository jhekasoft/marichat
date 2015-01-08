var Chat = {
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
    notifySection: 'chat',
    notifyMessageBeginEditing: 'beginEditing',
    notifyMessageCancelEditing: 'cancelEditing',
    notifyMessageAdded: 'added',
    notifyMessageUpdated: 'updated',
    notifyMessageDeleted: 'deleted',
    notifyMessageStatusChanged: 'statusChanged',
    notifyMessageTyping: 'isTyping',
    connectionCheckTimeout: null,
    checkConnectionTime: 10000,
    usernameTypingTime: 1000,
    usernameLastTyping: {},
    debug: false,
    add: function() {
        formData = $('.chat-add-form input').serialize();

        Chat.showAddSpinner();

        $.ajax({
            type: "POST",
            url: Chat.addUrl,
            data: formData
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.chat-items').html(response.listHtml);

                Chat.scrollToWrite();
            }

            if (response.addFormHtml) {
                $('.chat-add-form').html(response.addFormHtml);
            }

            Chat.hideAddSpinner();

            // Send notification for other users
            Chat.sendNotification({
                message: Chat.notifyMessageAdded
            });
        });
    },
    showAddSpinner: function() {
        $('.chat-add-form input').attr('disabled', true);
        $('.chat-add-form #chat_submit').hide();
        $('.chat-add-form .add-loading').show();
    },
    hideAddSpinner: function() {
        $('.chat-add-form input').attr('disabled', false);
        $('.chat-add-form #chat_submit').show();
        $('.chat-add-form .add-loading').hide();
    },
    scrollToWrite: function() {
        $('html, body').animate({
            scrollTop: $(".chat-add-form").offset().top
        }, 1000);
    },
    getEditForm: function(id) {
        Chat.isEditingRowNow = true;

        Chat.showRowLoadingSpiner(id);

        $.ajax({
            type: "POST",
            url: Chat.getEditFormUrl,
            data: {'id': id}
        }).done(function(response) {
            Chat.hideRowLoadingSpiner(id);

            if ('ok' == response.result && response.editFormHtml) {
                Chat.cancelAllEdit();

                $('.chat-row-form-' + id).html(response.editFormHtml);
                $('.chat-row-form-' + id).show();
                $('.chat-row-data-' + id).hide();
                $('.chat-actions-default-' + id).hide();
                $('.chat-actions-edit-' + id).show();

                // Send notification for other users
                Chat.sendNotification({
                    message: Chat.notifyMessageBeginEditing, id: id
                });
            }

            if (response.errorDetails) {
                alert(response.errorDetails);
            }
        });
    },
    showRowLoadingSpiner: function(id) {
        $('.chat-row-loading-' + id).css('display', 'inline-block');
    },
    hideRowLoadingSpiner: function(id) {
        $('.chat-row-loading-' + id).hide();
    },
    cancelEdit: function(id) {
        $('.chat-row-form-' + id).hide();
        $('.chat-row-data-' + id).show();
        $('.chat-actions-default-' + id).show();
        $('.chat-actions-edit-' + id).hide();

        Chat.isEditingRowNow = false;

        // Send notification for other users
        Chat.sendNotification({
            message: Chat.notifyMessageCancelEditing, id: id
        });
    },
    cancelAllEdit: function() {
        $('.chat-row-form').hide();
        $('.chat-row-data').show();
        $('.chat-actions-default').show();
        $('.chat-actions-edit').hide();
    },
    edit: function(id) {
        $.ajax({
            type: "POST",
            url: Chat.editUrl,
            data: $('.chat-row-form-' + id + ' input').serialize()
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.chat-items').html(response.listHtml);

                Chat.isEditingRowNow = false;

                // Send notification for other users
                Chat.sendNotification({
                    message: Chat.notifyMessageUpdated, id: id
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
            url: Chat.deleteUrl,
            data: {'id': id}
        }).done(function(response) {
            $('.chat-items').html(response.html);

            // Send notification for other users
            Chat.sendNotification({
                message: Chat.notifyMessageDeleted, id: id
            });
        });
    },
    refreshList: function(afterCallback) {
        $.ajax({
            type: "POST",
            url: Chat.getListUrl,
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.chat-items').html(response.listHtml);
                if (afterCallback) {
                    afterCallback();
                }
            }
        });
    },
    refreshListIfNotEditing: function() {
        if (this.isEditingRowNow) {
            return false;
        }

        this.refreshList();
    },
    refreshListGui: function() {
        this.showMessagesSpinner();
        this.refreshList(function() {Chat.hideMessagesSpinner()});
    },
    showMessagesSpinner: function() {
        $('.messages-refresh-block .btn-messages-refresh').hide();
        $('.messages-refresh-block .messages-loading').show();
    },
    hideMessagesSpinner: function() {
        $('.messages-refresh-block .btn-messages-refresh').show();
        $('.messages-refresh-block .messages-loading').hide();
    },
    changeStatus: function(id, status) {
        $.ajax({
            type: "POST",
            url: Chat.changeStatusUrl,
            data: {id: id, status: status}
        }).done(function(response) {
            if ('ok' == response.result && response.listHtml) {
                $('.chat-items').html(response.listHtml);

                // Send notification for other users
                Chat.sendNotification({
                    message: Chat.notifyMessageStatusChanged, id: id
                });
            }
        });
    },
    createSocketConnection: function() {
        this.webSocketConnection = new WebSocket(this.webSocketsUlr);

        this.webSocketConnection.onopen = function(e) {
            if (Chat.debug) {
                console.log('Connected');
            }

            Chat.refreshList();
            Chat.onlineStatusOn();
        };

        this.webSocketConnection.onclose = function(e) {
            if (Chat.debug) {
                console.log('Connection closed');
            }

            Chat.onlineStatusOff();

            Chat.connectionCheckTimeout = setTimeout(function() {
                Chat.checkSocketConnection();
            }, Chat.checkConnectionTime);
        };

        this.webSocketConnection.onmessage = function(e) {
            if (Chat.debug) {
                console.log('Recieved message ' + e.data);
            }

            Chat.handleNotification(e.data);
        };
    },
    isOnline: function() {
        return Chat.webSocketConnectionIsOpened;
    },
    onlineStatusOn: function() {
        Chat.webSocketConnectionIsOpened = true;

        $('.online-status-online').show();
        $('.online-status-offline').hide();
    },
    onlineStatusOff: function() {
        Chat.webSocketConnection = null;
        Chat.webSocketConnectionIsOpened = false;

        $('.online-status-online').hide();
        $('.online-status-offline').show();
    },
    checkSocketConnection: function() {
        if (this.isOnline()) {
            clearTimeout(this.connectionCheckTimeout)
        } else if(!Chat.webSocketConnection) {
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

        if (Chat.debug) {
            console.log('Sended message ' + $.toJSON(messageJson));
        }
    },
    handleNotification: function(message) {
        messageJson = $.evalJSON(message);

        if (messageJson.section != this.notifySection) {
            return false;
        }

        if (messageJson.message == Chat.notifyMessageBeginEditing) {
            $('.chat-row-editing-' + messageJson.id).show();
        }

        if (messageJson.message == Chat.notifyMessageCancelEditing) {
            $('.chat-row-editing-' + messageJson.id).hide();
        }

        if (messageJson.message == Chat.notifyMessageUpdated) {
            $('.chat-row-editing-' + messageJson.id).hide();
            this.refreshListIfNotEditing();
        }

        if (messageJson.message == Chat.notifyMessageTyping) {
            this.showUserIsTyping(messageJson.username);
        }

        if (messageJson.message == Chat.notifyMessageAdded ||
            messageJson.message == Chat.notifyMessageDeleted ||
            messageJson.message == Chat.notifyMessageStatusChanged) {
            this.refreshListIfNotEditing();
        }
    },
    notifyTyping: function(username) {
        // Send notification for other users
        Chat.sendNotification({
            message: Chat.notifyMessageTyping, username: username
        });
    },
    showUserIsTyping: function(username) {
        $('.alert-chat-typing-username').text(username);
        $('.alert-chat-typing').show();
        Chat.usernameLastTyping = {'timestamp': new Date().getTime()};
        setTimeout(function() {
            var nowTimestamp = new Date().getTime();
            if (Chat.usernameTypingTime <= nowTimestamp - Chat.usernameLastTyping.timestamp) {
                $('.alert-chat-typing').hide();
            }
        }, Chat.usernameTypingTime);
    }
};
