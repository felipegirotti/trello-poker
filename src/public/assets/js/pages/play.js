var trelloPoker = {};
$(function() {
    trelloPoker = new TrelloPoker();
    trelloPoker.authorize(TrelloPokerPlay);
    $('#btn-fechar').attr('disabled', true);
    //EVENTO DE CLICK NO BOARD
    $('#boards a').live('click', function(e) {
        e.preventDefault();
        trelloPoker.cardsMembers(this.id, $(this));
        return false;
    });
    
    $('#btn-vote').click(function(e) {
        e.preventDefault();
        if (TrelloPokerPlay.validate()) {
            TrelloPokerPlay.addVote($('.form-game-pmeter'));
        }
    });
    
    $('#btn-fechar').click(function(e) {
        e.preventDefault();
        TrelloPokerPlay.closeCard();
    });
    
    $('.form-game-pmeter').live('submit', function(e) {
        e.preventDefault();
        
        return false;
    });
    
    setInterval(function(){
        TrelloPokerPlay.getUsers()
    } , 15000);
});

TrelloPokerPlay = {
    trelloVisible: $('.trello-visible'),
    idPoker: '',
    user: {},
    owner: '',
    init: function() {        
        if (!Trello.authorized()) {
            location.href='/';
        } else {
            this.idPoker = $('#game-poker').attr('data-id-poker');
            TrelloPokerPlay.trelloVisible.show();   
            TrelloPokerPlay.getCard($('div[data-card-id]').attr('data-card-id'));                      
        }
    },
    userFinish: function() {
        TrelloPokerPlay.user = trelloPoker.user;        
        TrelloPokerPlay.owner = $('#id-owner').val();
        $('#users li[data-id-member="'+ TrelloPokerPlay.user.id +'"]').removeClass('status-off').addClass('status-on');
        if (this.isOwner())
            $('.owner-game').show();
        this.getUsers();
    },
    getUsers: function() {
        var dataPost = {
            member_id : TrelloPokerPlay.user.id, 
            poker_id : TrelloPokerPlay.idPoker,
            card_id : $('div[data-card-id]').attr('data-card-poker')
        };
        $.post('/poker/play/users/', dataPost, function (response) {
            var objHtml = TrelloRender.renderUsersPlay(response, TrelloPokerPlay.user.id);
            $('#users').html(objHtml.users);
            $('#card-game-vote').html(objHtml.votes);
            if (objHtml.memberIsVoted) {
                TrelloPokerPlay.blockBtn();
                TrelloPokerPlay.blockInputs();
            }
            if (objHtml.totalUsers == objHtml.totalVotes) {
                $('#btn-fechar').attr('disabled', false);
                $('#vote-total').parents('.form-group').removeClass('hide');
                TrelloPokerPlay.blockBtn();
                TrelloPokerPlay.blockInputs();
            }
        });
    },
    getCard: function(idCard) {
        Trello.get('card/' + idCard, function (response) {
            $('#card-info').html(TrelloRender.renderCardInfo(response));
        });
    },
    addVote: function(form) {
        $.post('/poker/play/vote/' + TrelloPokerPlay.user.id, form.serialize(), function(response) {
            TrelloPokerPlay.blockBtn();
            TrelloPokerPlay.getUsers();
        });
    },
    validate: function() {
        var valid = true;
        $('.validate-error').remove();
        $('.form-game-pmeter .require').each(function() {           
            if ($(this).val() == '') {
                valid = false;
                TrelloRender.validateInput($(this), 'Campo obrigatório');
            }
        });
        return valid;
    },
    blockBtn: function() {
        $('#btn-vote, #btn-pular').attr('disabled', true);
    },
    blockInputs: function() {
        $('input.require, select.require').attr('disabled', true);
    },
    closeCard: function() {
        var dataPost = {
            member_id : TrelloPokerPlay.user.id,             
            card_id : $('div[data-card-id]').attr('data-card-poker'),
            pontuacao: $('#vote-total').val()
        };
        $.post('/poker/play/close/', dataPost, function(response) {
            if (response.success) {
                var cardNameAtual = $('#card-info h3 a').text().replace(/\(.*?\)/, '');
                Trello.rest('PUT', 
                    'card/' + response.card, 
                    {value : '('+ dataPost.pontuacao +')' + cardNameAtual},
                    function (dataResponseTrello) {
                        console.log(dataResponseTrello)
                    }
                );
            }
        }, 'json');
    },
    isOwner: function() {
        return TrelloPokerPlay.owner == TrelloPokerPlay.user.id;
    }
};