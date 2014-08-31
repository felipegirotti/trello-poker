var trelloPoker = {};
$(function() {
    trelloPoker = new TrelloPoker();
    trelloPoker.authorize(TrelloPokerPlay);
    $('#btn-regame').attr('disabled', true);
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
    
    $('#btn-voto-total').click(function(e) {
        e.preventDefault();
        TrelloPokerPlay.addVoteTotalCard();
    })
    
    $('#btn-regame').click(function(e) {
        e.preventDefault();
        var cardId = $('input[name="card_id"]').val();
        TrelloPokerPlay.regame(cardId);
    });
    
    $('.form-game-pmeter').live('submit', function(e) {
        e.preventDefault();        
        return false;
    });         
    
    $('.btn-remove-user').live('click', function(){
        TrelloPokerPlay.removeUser($(this).parent().attr('data-id-member'));
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
        this.finishGame();
        this.getCardFinished();
        this.getUsers();
    },
    getUsers: function() {
        var dataPost = {
            member_id : TrelloPokerPlay.user.id, 
            poker_id : TrelloPokerPlay.idPoker,
            card_id : $('div[data-card-id]').attr('data-card-poker')
        };
        $.post('/poker/play/users/', dataPost, function (response) {
            var reloadPage = response.users.filter(function(user){
                return user.member_id == TrelloPokerPlay.user.id && user.update_page == 1;
            });
            if (reloadPage.length > 0) {
                location.reload();
            }
            var isOwner = TrelloPokerPlay.isOwner();
            var objHtml = TrelloRender.renderUsersPlay(response, TrelloPokerPlay.user.id, isOwner);
            $('#users').html(objHtml.users);
            $('#card-game-vote').html(objHtml.votes);
            $('#btn-voto-total').attr('disabled', true);
            if (objHtml.memberIsVoted) {
                TrelloPokerPlay.blockBtn();
                TrelloPokerPlay.blockInputs();
            }
            if (objHtml.totalUsers == objHtml.totalVotes) {               
                $('#vote-total').parents('.form-group').removeClass('hide');
                TrelloPokerPlay.blockBtn();
                TrelloPokerPlay.blockInputs();
                 $('#btn-voto-total, #btn-regame').attr('disabled', false);
            }
            if (response.pontuacao != 0) {                
                TrelloPokerPlay.blockBtn();
                TrelloPokerPlay.blockInputs();
                $('#btn-voto-total').attr('disabled', true);
                $('#btn-fechar').attr('disabled', false);
            }
            if (response.status == 0 && !isOwner) 
                location.reload();            
            
            if (isOwner) {
                var pokerId = $('input[name=poker_id]').val();
                $('#usuarios-name').html('Usuários <a href="/poker/play/add-user/'+ pokerId +'" id="btn-add-user"><i class="glyphicon glyphicon-plus-sign btn-add-user"></i></a>');                        
                $('#btn-add-user').colorbox({
                    iframe: true,
                    width: '300',
                    height: '250'
                });
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
        $('#btn-vote, #btn-fechar, #btn-voto-total').attr('disabled', true);
    },
    blockInputs: function() {
        $('input.require, select.require').attr('disabled', true);
    },
    addVoteTotalCard: function() {
        var dataPost = {
            member_id : TrelloPokerPlay.user.id,             
            card_id : $('div[data-card-id]').attr('data-card-poker'),
            pontuacao: $('#vote-total').val()
        };
        $.post('/poker/play/close/', dataPost, function(response) {
            if (response.success) {
                var cardNameAtual = $('#card-info h3 a').text().replace(/\(.*?\)/, '');              
                Trello.rest('PUT', 
                    'card/' + response.success.card + '/name', 
                    {value : '('+ dataPost.pontuacao +')' + cardNameAtual},
                    function (dataResponseTrello) {
                        location.reload();
                    }
                );
            }
        }, 'json');
    },
    isOwner: function() {
        return TrelloPokerPlay.owner == TrelloPokerPlay.user.id;
    },
    finishGame: function() {
        var card_id = $('div[data-card-id]').attr('data-card-poker');
        if (card_id == '') {
            TrelloRender.validateInput($('.meio-container > .jumbotron').next(), 'Game Encerrado');
            $('.form-game-pmeter').hide();            
        } 
    },
    getCardFinished: function() {
        $('#finish-cards li').each(function() {
            var li = $(this), vote = li.attr('data-card-vote');            
            Trello.get('card/' + li.attr('data-id-card-finish'), function (response) {                
                li.html(TrelloRender.listCards(response, vote, TrelloPokerPlay.isOwner()));
            });
        });
    },
    regame: function(cardId) {
        if (this.isOwner()) {
            var dataPost = {
                poker_id: $('input[name="poker_id"]').val(),
                card_id : cardId,
                member_id : TrelloPokerPlay.user.id
            };
            $.post('/poker/play/regame', dataPost, function(response) {                
                if (response.success && response.success.code == 202) {
                    location.reload();
                }
            });
        }
    },
    removeUser: function(memberId) {
        if (this.isOwner()) {
            var dataPost = {
                poker_id: $('input[name="poker_id"]').val(),
                member_id: memberId,
                owner: TrelloPokerPlay.user.id
            };
            $.ajax({
                url: '/poker/play/add-user/' + dataPost.poker_id,
                type: 'DELETE',
                dataType: 'json',
                data: dataPost,
                complete: function(response) {
                    TrelloPokerPlay.getUsers();
                }
            });
        }
    }
};