define([
    'ko',
    'jquery',
    'uiComponent'
], function (ko, $, Component) {
    'use strict';

    return Component.extend({
        items: ko.observableArray([]),
        title: ko.observable(''),
        publishDate: ko.observable(''),

        initialize: function () {
            this._super();
            this.fetch();
        },

        fetch: function () {
            $.getJSON(this.fetchUrl, function (data) {
                console.log(data);
                this.title(data.channel.title);
                this.publishDate("Published on: " + data.channel.pubDate.split("+")[0].trim());
                this.items(data.channel.item);
            }.bind(this))
        }
    });
});
