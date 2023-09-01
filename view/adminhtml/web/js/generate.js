define([
    'jquery',
    'prototype',
], function ($) {
     let exportConfig = function (config) {
        params = {};
        new Ajax.Request(config.urlGenerate, {
            loaderArea: true,
            asynchronous: true,
            method: 'get',
            parameters: params,
            onSuccess: function (transport) {
                let response = JSON.parse(transport.responseText);
                $('#messages .message-success span.message-text').text(response.message);
                let messages = $('#messages .message-success');
                messages.show();
                messages.delay(8000).fadeOut();
            },
            onFailure: function(transport) {
                let response = JSON.parse(transport.responseText);
                $('#messages .message-error span.message-text').text(response.message);
                let messages = $('#messages .message-error');
                messages.show();
                messages.delay(8000).fadeOut();
            }
        });
    }
    return function (config) {
        $('#exportbtn').click(function () {
            exportConfig(config);
        });
    }
});