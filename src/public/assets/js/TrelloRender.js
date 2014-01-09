TrelloRender = {
    renderCard: function(card) {
        return TrelloRender.renderOptions(card, 'name', 'trello-card-option', 'label-primary', 'card[]');
    },
    renderMembers: function(member) {
        return TrelloRender.renderOptions(member, 'fullName', 'trello-member-option', 'label-info', 'member[]');
    },
    renderOptions: function(obj, objName, classOption, classLabel, nameCheckbox) {
        var html;
        html = '<div class="checkbox">';
        html += '<label>';
        html += '<input type="checkbox" name="' + nameCheckbox + '" class="' + classOption + '" value="' + obj.id + '" />';
        html += '<input type="hidden" name="name-' + nameCheckbox + '" value="' + obj[objName] + '" />';
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
    renderUsersPlay: function(dataUsers, memberId) {
        var html = { users: '', votes : '<div class="row">', totalUsers: 0, totalVotes : 0, memberIsVoted: false};
        for (var index in dataUsers) {
            var user = dataUsers[index];
            var status = user.logged == 0 ? 'off' : 'on';
            var votes = this.renderVoteCard(user);
            html.users += '<li class="status-'+ status +'" data-id-member="'+ user.member_id +'">'+ user.fullname +'</li>';            
            if (votes !== null) {
                html.votes += votes;
                html.totalVotes++;
                if (memberId == user.member_id) {
                    html.memberIsVoted = true;
                }
            }
            html.totalUsers++;
        }
        html.votes += '</div>';
        return html;
    },
    renderVoteCard: function(dataUser) {
        if (dataUser.pontuacao !== null) {
            var html = '';
            html = '<div class="vote-card col-lg-2">';
            html += '<div class="vote-number">'+ dataUser.pontuacao +'</div>';
            html += '<div class="vote-user">'+ dataUser.fullname +'</div>';
            html += '</div>';
            return html;
        }
        return null;
    },
    validateInput: function(el, msg) {
        var html = '<div class="alert alert-danger validate-error">'+ msg +'</div>';                       
        el.before(html);
    }
};


