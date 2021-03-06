$(function() {
   $('input.maskNum').keypress(function(e){
        if((e.which < 46 || e.which > 57) && (e.which != 8) && (e.which != 9) ){
            return false;
        }
    });
    $('#btn-logout').live('click', function(){
       Trello.deauthorize();
       location.href = '/';
    });
});


function TrelloPoker() {
    this.user = {};
}

TrelloPoker.prototype = {
    constructor : 'TrelloPoker',
    authorize: function(obj) {
        var parentThis = this;
        Trello.authorize({
            name: 'Trello Poker',
            scope: {read: true, write: true},
            success: function() {                
                obj.init();
                parentThis.getUser(obj);
                
            }, error: function() {
                console.log('erro', data);
            }
        });
    },
    getUser: function(obj) {
        var parentThis = this;
        Trello.get('members/me', function(responseUser) {            
            parentThis.user = responseUser;
            var html;
            html = responseUser.fullName;
            if (responseUser.avatarHash) {
                html += ' - <img class="member-avatar" height="30" width="30" \n\
					 src="https://trello-avatars.s3.amazonaws.com/' + responseUser.avatarHash + '/30.png" >';
            }
            html += '<span class="btn btn-primary btn-mini" id="btn-logout">Logout</span>';
            $('.trello-user').html(html);
            $('#my-pokers-link').attr('href', '/my/' + responseUser.id );
            $('#data-user').attr('data-user', JSON.stringify(responseUser));
            if (typeof obj.userFinish == 'function') {
                obj.userFinish();
            }
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
        var elementAppend, htmlInnerForm, elementForm, prependPainel;
        prependPainel = '<div class="panel panel-info"><div class="panel-heading"><h3 class="panel-title">Options for game</h3> </div> <div class="panel-body">';
        elementForm = element.next('form.form-add-to-poker');
        htmlInnerForm = '<div id="cards-' + idBoard + '">'
            + '<div class="row"><div class="col-lg-6"></div><div class="col-lg-6"></div></div></div>';
        if (elementForm.length == 0) {
            element.after('<form class="form-add-to-poker" data-id-board="' + idBoard + '">'+ prependPainel + htmlInnerForm +'</div></div></form>');
            elementForm = element.next('form');
        } else if (elementForm.length > 0
             && elementForm.find('div#cards-' + idBoard).length == 0) {
            elementForm.html(prependPainel + htmlInnerForm);
        }
        
        if (elementForm.length > 0 && elementForm.find('input[type="checkbox"]').length == 0) {
            elementAppend = element.next('form').find('div.panel-body');
            elementAppend.append('<div class="row">\n\
                        <div class="form-group col-lg-4"> \n\
                            <label for="nome-' + idBoard + '">Name of the game</label>\n\
                            <input id="nome-' + idBoard + '" class="form-control" placeholder="Name of the game" value="' + element.text() + '" type="text" required name="nome" class="" />\n\
                        </div>\n\
                        <input type="hidden" name="board-id" value="'+ idBoard +'" />\n\
                        <input type="hidden" name="user-id" value="'+ this.user.id +'" />\n\
                        <input type="hidden" name="user-name" value="'+ this.user.fullName +'" />\n\
                    </div>');
            elementAppend.append('<button class="btn btn-primary add-to-poker">Start the game</button>');
            this.getMembers(idBoard, elementAppend.find('.col-lg-6:last'));
            this.getCards(idBoard, elementAppend.find('.col-lg-6:first'));
            console.log('Criar');
        }
        
        console.log(elementForm.length);
       
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
        element.append('<h2>Members</h2>');
        var parentThis = this;
        Trello.get('boards/' + idBoard + '/members', function(responseMembers) {
            $.each(responseMembers, function(i, member) {
                if (parentThis.user.id != member.id)
                    element.append(TrelloRender.renderMembers(member));
            });
        });
    },
    
};

