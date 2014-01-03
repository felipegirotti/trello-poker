
function TrelloPoker() {
    this.user = {};
}

TrelloPoker.prototype = {
    constructor : 'TrelloPoker',
    authorize: function(obj) {
        var parentThis = this;
        Trello.authorize({
            name: 'Trello Poker',
            scope: {read: 'allowRead', write: 'allowWrite'},
            success: function() {                
                obj.init();
                parentThis.getUser();
                
            }, error: function() {
                console.log('erro', data);
            }
        });
    },
    getUser: function() {
        var parentThis = this;
        Trello.get('members/me', function(responseUser) {            
            parentThis.user = responseUser;
            var html;
            html = responseUser.fullName;
            if (responseUser.avatarHash) {
                html += ' - <img class="member-avatar" height="30" width="30" \n\
					 src="https://trello-avatars.s3.amazonaws.com/' + responseUser.avatarHash + '/30.png" >';
            }
            $('.trello-user').html(html);
            $('#my-pokers-link').attr('href', '/my/' + responseUser.id );
        });
    },
    getBoards: function() {
        Trello.get('members/me/boards', function(data) {
            $.each(data, function(i, board) {
                $('<a>')
                    .attr({href: board.url, id: board.id})
                    .addClass('board')
                    .text(board.name)
                    .appendTo('#boards');
            });
        });
    },
    cardsMembers: function(idBoard, element) {
        var elementAppend;
        element.after('<form class="form-add-to-poker" data-id-board="' + idBoard + '"><div id="cards-' + idBoard + '"><div class="row"><div class="col-lg-6"></div><div class="col-lg-6"></div></div></div></form>');
        elementAppend = element.next('form').children('div');
        elementAppend.append('<div class="row">\n\
                    <div class="form-group col-lg-4"> \n\
                        <label for="nome-' + idBoard + '">Nome Poker</label>\n\
                        <input id="nome-' + idBoard + '" class="form-control" placeholder="Nome do jogo" value="' + element.text() + '" type="text" required name="nome" class="" />\n\
                    </div>\n\
                    <input type="hidden" name="board-id" value="'+ idBoard +'" />\n\
                    <input type="hidden" name="user-id" value="'+ this.user.id +'" />\n\
                </div>');
        elementAppend.append('<button class="btn btn-primary add-to-poker">Adicionar para o poker</button>');
        this.getMembers(idBoard, elementAppend.find('.col-lg-6:last'));
        this.getCards(idBoard, elementAppend.find('.col-lg-6:first'));
    },
    getCards: function(idBoard, element) {
        Trello.get('boards/' + idBoard + '/cards', function(responseCards) {
            element.append('<h2>Cards</h2>');
            $.each(responseCards, function(i, card) {
                if (!card.closed)
                    element.append(TrelloRender.renderCard(card));
            });
        });
    },
    getMembers: function(idBoard, element) {
        element.append('<h2>Membros</h2>');
        var parentThis = this;
        Trello.get('boards/' + idBoard + '/members', function(responseMembers) {
            $.each(responseMembers, function(i, member) {
                if (parentThis.user.id != member.id)
                    element.append(TrelloRender.renderMembers(member));
            });
        });
    },
    
};

