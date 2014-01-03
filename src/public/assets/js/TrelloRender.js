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
        html += '<div class="label ' + classLabel + '">' + obj[objName] + '</div>';
        html += '</label>';
        html += '</div>';
        return html;
    }
};


