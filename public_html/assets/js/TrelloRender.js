TrelloRender = {
    renderCard: function(card) {
        return TrelloRender.renderOptions(card, 'name', 'trello-card-option', 'label-primary', 'card');
    },
    renderMembers: function(member) {
        return TrelloRender.renderOptions(member, 'fullName', 'trello-member-option', 'label-info', 'member');
    },
    renderOptions: function(obj, objName, classOption, classLabel, nameCheckbox) {
        var html, value;
        value = nameCheckbox == 'member' 
                ? '{"id" : "' + obj.id + '", "name" : "' + obj[objName] + '"}'
                : obj.id;
        html = '<div class="checkbox">';
        html += '<label>';
        html += '<input type=\'checkbox\' name=\'' + nameCheckbox + '[]\' class=\'' + classOption + '\' value=\'' + value + '\' />';        
        html += '<div class="label ' + classLabel + '">' + obj[objName] + '</div>';
        html += '</label>';
        html += '</div>';
        return html;
    },
    renderCardInfo: function (dataCard) {
        var html;
        html = '<h3><a href="'+ dataCard.url +'" target="_blank">'+ dataCard.name +'</a></h3>';
        html += '<div>'+ dataCard.desc +'</div>';
        return html;
    },
    renderUsersPlay: function(dataUsers, memberId, isOwner) {
        var html = { users: '', votes : '<div class="row">', totalUsers: 0, totalVotes : 0, memberIsVoted: false};
        html.totalUsers = dataUsers.users.length;
        html.totalVotes = dataUsers.users.filter(function(objUser) {return objUser.pontuacao !== null}).length;
        
        for (var index in dataUsers.users) {
            var user, status, memberIsVoted, votes;
            user = dataUsers.users[index];
            status = user.logged == 0 ? 'off' : 'on';
            memberIsVoted = (memberId == user.member_id);
            votes = this.renderVoteCard(user, html.totalUsers == html.totalVotes, memberIsVoted);
            html.users += '<li class="status-'+ status +'" data-id-member="'+ user.member_id +'">';
            html.users += user.fullname;
            if (isOwner && !memberIsVoted)
                html.users += ' <i class="glyphicon glyphicon-remove-circle btn-remove-user"></i>'
            html.users += '</li>';
            if (votes !== null) {
                html.votes += votes;                
                if (memberIsVoted) {
                    html.memberIsVoted = true;
                }
            }            
        }
        html.votes += '</div>';
        return html;
    },
    renderVoteCard: function(dataUser, pontuacao, memberIsVoted) {
        if (dataUser.pontuacao !== null) {
            var html = '', pontos;
            pontos = pontuacao != 0 || memberIsVoted ? 
                        '<div class="vote-number">'+ dataUser.pontuacao +'</div>' :
                        '<div class="circle-poker">Poker</div>';
            html = '<div class="vote-card col-lg-2">';
            html += pontos;
            html += '<div class="vote-user">'+ dataUser.fullname +'</div>';
            html += '</div>';
            return html;
        }
        return null;
    },
    validateInput: function(el, msg) {
        var html = '<div class="alert alert-danger validate-error">'+ msg +'</div>';                       
        el.before(html);
    },
    listCards: function(dataCard, vote) {        
        var html = '';
        html += '<a href="'+ dataCard.url +'" target="_blank">';
        html += dataCard.name.replace(/\(.*?\)/, '');
        html += '</a>';
        if (vote != 0) 
            html += ' - Pontos: <b>' + vote + '</b>';                     
        return html;
    }
};


