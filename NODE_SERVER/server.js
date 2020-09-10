/*jshint esversion: 6 */
/*
var app = require('http').createServer();

var io = require('socket.io')(app);

var LoggedUsers = require('./loggedusers.js');

app.listen(8080, function(){
    console.log('listening on *:8080');
});

app.post("/like", function(req, res) {
    var params = req.body;
    var clients = io.sockets.clients().sockets;
	for (const key in clients) {
	        if (key != params.id) clients[key].emit("access", params);
	    }
	res.send();
});
// ------------------------
// Estrutura dados - server
// ------------------------

// loggedUsers = the list (map) of logged users. 
// Each list element has the information about the user and the socket id
// Check loggedusers.js file

let loggedUsers = new LoggedUsers();

io.on('connection', function (socket) {
    console.log('client has connected (socket ID = '+socket.id+')' );
	socket.on('user_enter', function (user) {
		if (user !== undefined && user !== null) {
			socket.join('type_' + user.type);
			loggedUsers.addUserInfo(user, socket.id);
		}
	});

	socket.on('text_from_worker_to_managers', function (msg, userInfo) {
		if (userInfo !== undefined) {
			let channelName = 'type_' +"manager";
			io.sockets.to(channelName).emit('text_from_server_managers', userInfo.name +': "' + msg + '"');
		}
	});
	socket.on('detected_rfid', function (changedUser) {
		//socket.broadcast.emit('meal_terminated_noti', changedUser);
		if (changedUser !== undefined) {
			let channelName = 'type_' +"admin";
			io.sockets.to(channelName).emit('detected_rfid', changedUser);
		}
	});

	socket.on('user_exit', function (user) {
		if (user !== undefined && user !== null) {
			socket.leave('department_' + user.type);
			loggedUsers.removeUserInfoByID(user.id);
		}
	});
});
*/
const express = require("express");
const socketio = require("socket.io");
const http = require("http");
const app = express();
server = http.createServer(app);
const io = socketio(server);

var LoggedUsers = require('./loggedusers.js');

const clients = [];
app.use(
    express.urlencoded({
        extended: true
    })
);
/**
 * Initialize Server
 */
server.listen(8080, function() {
    console.log("Servidor a Escutar na Porta 8080");
});
/**
 * Página de Teste
 */
app.get("/", function(req, res) {
    console.log("ououuouo");
    res.send("Servidor Rodando...");
});
app.post("/check_danger", function(req, res) {
	let channelName = 'type_' +"admin";
	var danger='';
	var message='';
	if(req.body.danger){//user a entrar em sala ocupada
	    console.log("Possible Hazard detected at room :"+req.body.room);
		danger='danger';
		message="Possible Hazard detected at room :"+req.body.room;
	}

	io.sockets.clients().to(channelName).emit(danger,message);
	console.log("enviouuuuu");
	res.send();
});

// Recebe requisição do Laravel
app.post("/check_rfid", function(req, res) {
	let channelName = 'type_' +"admin";
	var checkAccess='';
	var access='';
	let ver = 0;
	//console.log("recer POST")
	//console.log(res)
    //    console.log(req.params)
     //   console.log(req.body)
	//io.sockets.clients().to(channelName).emit('access_granted','boas');

	if(req.body.rfid_id) {//se existir rfid_id é porque foi nao autorizado
	    console.log("ACCESS DENIED DETECTED"+req.body.rfid_id);
		checkAccess='access_denied';
		access='ID:['+req.body.rfid_id+'] tried to enter Room: ['+req.body.room+'] ';
	}
	else if(req.body.entering){//user entra na sala
	    console.log("ACCESS TO ROOM:"+req.body.room);
		checkAccess='access_granted';
		access='User: ['+req.body.name+'] entered Room: ['+req.body.room+']';
		ver=0;
	}
	else if(req.body.leaving){//user sair da sala
	    console.log("LEAVING ROOM:"+req.body.room);
		checkAccess='access_granted';
		access='User: ['+req.body.name+'] left Room: ['+req.body.room+']';
		ver=0;
	}
	else if(req.body.occupied){//user a entrar em sala ocupada
	    console.log("ACCESS TO ROOM:"+req.body.room);
		checkAccess='access_denied';
		access='User: ['+req.body.name+'] trying to enter Room: ['+req.body.room+']';
	}
	else if(req.body.noPremission){//user a entrar em sala ocupada
	    console.log("ACCESS TO ROOM:"+req.body.room);
		checkAccess='access_denied';
		access='Someone without permission tried to enter Room: ['+req.body.room+']';
	}
	/*else if(req.body.name){
	    console.log("ACCESS GRANTED DETECTED"+req.body.name);
		checkAccess='access_granted';
		access=req.body.name;
	}*/

	io.sockets.clients().to(channelName).emit(checkAccess,access);
	if(ver == 1){
		let channelName = 'type_' +"client";
		io.sockets.clients().to(channelName).emit(checkAccess,access);
		ver=0;
	}
	console.log("enviouuuuu");
	res.send();
});

app.post("/check_finger", function(req, res) {
	let channelName = 'type_' +"admin";
	var checkAccess='';
	var access='';
	//console.log("recer POST")
	//console.log(res)
    //    console.log(req.params)
     //   console.log(req.body)
	//io.sockets.clients().to(channelName).emit('access_granted','boas');

	if(req.body.finger_id) {//se existir finger_id é porque foi nao autorizado
	    console.log("ACCESS DENIED DETECTED"+req.body.finger_id);
		checkAccess='access_denied';
		access='ID:['+req.body.finger_id+'] tried to enter Room: ['+req.body.room+'] ';
	}
	else if(req.body.entering){//user entra na sala
	    console.log("ACCESS TO ROOM:"+req.body.room);
		checkAccess='access_granted';
		access='User: ['+req.body.name+'] entered Room: ['+req.body.room+']';
		ver=0;
	}
	else if(req.body.leaving){//user sair da sala
	    console.log("LEAVING ROOM:"+req.body.room);
		checkAccess='access_granted';
		access='User: ['+req.body.name+'] left Room: ['+req.body.room+']';
		ver=0;
	}
	else if(req.body.occupied){//user a entrar em sala ocupada
	    console.log("ACCESS TO ROOM:"+req.body.room);
		checkAccess='access_denied';
		access='User: ['+req.body.name+'] trying to enter Room: ['+req.body.room+']';
	}
	else if(req.body.noPremission){//user a entrar em sala ocupada
	    console.log("ACCESS TO ROOM:"+req.body.room);
		checkAccess='access_denied';
		access='Someone without permission tried to enter Room: ['+req.body.room+']';
	}
	/*else if(req.body.name){
	    console.log("ACCESS GRANTED DETECTED"+req.body.name);
		checkAccess='access_granted';
		access=req.body.name;
	}*/

	io.sockets.clients().to(channelName).emit(checkAccess,access);
	if(ver == 1){
		let channelName = 'type_' +"client";
		io.sockets.clients().to(channelName).emit(checkAccess,access);
		ver=0;
	}
	console.log("enviouuuuu");
	res.send();
});

let loggedUsers = new LoggedUsers();
// Recebe conexão dos usuários no servidor
io.on('connection', function (socket) {
    console.log('client has connected (socket ID = '+socket.id+')' );
	socket.on('user_enter', function (user) {
		    console.log("user");
		if (user !== undefined && user !== null) {
			socket.join('type_' + user.type);
			//socket.join('type_admin'); 
		    console.log("user");
		    console.log(user);
			loggedUsers.addUserInfo(user, socket.id);
		}
	});

	socket.on('text_from_worker_to_managers', function (msg, userInfo) {
		if (userInfo !== undefined) {
			let channelName = 'type_' +"manager";
			io.sockets.to(channelName).emit('text_from_server_managers', userInfo.name +': "' + msg + '"');
		}
	});
	socket.on('detected_rfid', function (changedUser) {
		//socket.broadcast.emit('meal_terminated_noti', changedUser);
		if (changedUser !== undefined) {
			let channelName = 'type_' +"admin";
			io.sockets.to(channelName).emit('detected_rfid', changedUser);
		}
	});

	socket.on('user_exit', function (user) {
		if (user !== undefined && user !== null) {
			socket.leave('department_' + user.type);
			loggedUsers.removeUserInfoByID(user.id);
		}
	});
});
