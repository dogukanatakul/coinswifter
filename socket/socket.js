'use strict';
var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var ioRedis = require('ioredis');

var redisPort = 6379;
var redisHost = "127.0.0.1";
var redis = new ioRedis(redisPort, redisHost);
redis.subscribe('tester', function (err, count) {
    console.log('done');
});
redis.on('App\\Events\\ActionEvent', function (channel, message) {
    console.log(channel, message)
});

var broadcastPort = 5000;
server.listen(broadcastPort, function () {
    console.log('Socket server is running.');
});