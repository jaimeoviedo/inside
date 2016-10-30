var my = my || {};



$(function () {
	
	my.Post = function () {
        var self = this;
        self.title = ko.observable("");
        self.hora = ko.observable("");
        self.dia = ko.observable("");
        self.pathImage = ko.observable("");
    };
    
    my.Post.prototype.parsearJson = function (post) {
        var self = this;
        self.title(post.title);
        self.hora(post.hora);
        self.dia(post.dia);
        self.pathImage(post.pathImage);
    };
    
    
});

