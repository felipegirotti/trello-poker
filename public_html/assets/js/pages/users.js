var trelloPoker = {};
$(function() {
    trelloPoker = new TrelloPoker();
    trelloPoker.authorize(TrelloPokerUsers);
    $('#form-user').submit(function(e){
        e.preventDefault();
        e.returnValue = false;
        $(this).find('div.error').remove();
        if ($('.trello-member-option').is(':checked')) 
            TrelloPokerUsers.addUser($(this).serialize());
        else 
            $(this).find('button').before('<div class="error alert alert-danger" style="margin-top:10px">Escolha ao menos um membro</div>');
        
        return false;
    });
});


TrelloPokerUsers = {
    trelloVisible: $('.trello-visible'),
    idPoker: '',
    user: {},
    owner: '',
    init: function() {        
        if (!Trello.authorized()) {
            this.closePage();
        } else {
            this.idPoker = $('#game-poker').attr('data-id-poker');
            this.trelloVisible.show();                                     
        }
    },
    userFinish: function() {
        this.user = trelloPoker.user;        
        this.owner = $('#id-owner').val();
        $('#member_id').val(this.user.id);
        if ( ! this.isOwner()) 
            this.closePage();        
        this.getUsers();
    },
    isOwner: function() {
        return this.owner == this.user.id;
    },
    getUsers: function() {
        var self, idBoard;
        self = this;        
        idBoard = $('#board_id').val();
        Trello.get('boards/' + idBoard + '/members', function(responseMembers) {
            var element = $('#form-user #members');           
            $.each(responseMembers, function(i, member) {                
                var inArray = $.inArray(member.id, membros);
                if (inArray == -1)
                    element.append(TrelloRender.renderMembers(member));
            });
            $('#btn-add-user').removeClass('hidden').show();
        });
    },
    closePage: function() {
        if (parent.$.fn.colorbox)
                parent.$.fn.colorbox.close();
            location.href = '/';
    },
    addUser: function(formSerialize) {        
        $.post(location.pathname, formSerialize, function(response) {
            parent.location.reload();
            TrelloPokerUsers.closePage();
        });
    }
}