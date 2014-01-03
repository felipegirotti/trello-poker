var trelloPoker = {};
$(function() {
    trelloPoker = new TrelloPoker();
    trelloPoker.authorize(TrelloPokerIndex);
    //EVENTO DE CLICK NO BOARD
    $('#boards a').live('click', function(e) {
        e.preventDefault();
        trelloPoker.cardsMembers(this.id, $(this));
        return false;
    });
    $('.form-add-to-poker').live('submit', function(e) {
        e.preventDefault();
        var error;
        error = [];
        $(this).find('div.error').remove();
        //VALIDAR CARDS
        if (!$(this).find('input[name="card[]"]').is(':checked')) {
            error.push(' Selecione ao menos um card para o poker');
        }
        //VALIDAR USUARIOS
        if (!$(this).find('input[name="member[]"]').is(':checked')) {
            error.push(' Selecione ao menos um membro para o poker');
        }
        if (error.length > 0) 
            $(this).find('button').before('<div class="error alert alert-danger" style="margin-top:10px">'+ error.join('<br />') +'</div>');
        else 
            TrelloPokerIndex.addToPoker($(this));
        return false;
    });
});

TrelloPokerMyPokers = {
    trelloVisible: $('.trello-visible'),
    init: function() {
        console.log('Autorizado? ', Trello.authorized());
        if (!Trello.authorized()) {
            TrelloPokerMyPokers.trelloVisible.hide();
        } else {
            TrelloPokerMyPokers.trelloVisible.show();
            //CARREGANDO OS BOARDS
            TrelloPokerMyPokers.myBoards();            
        }
    },
    myBoards: function() {
        var data;
        data = form.serialize();
        $.post('/poker/add', data, function(response) {
            form.empty().remove();
        });
    }
};