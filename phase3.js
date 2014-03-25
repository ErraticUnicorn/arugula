// Include http module, 

//var http = require('http'),
// And mysql module you've just installed. 
    mysql = require("mysql");
	url = require("url"); 
	qs = require('querystring');

// Create the connection. 
// Data is default to new mysql installation and should be changed according to your configuration. 

var connection = mysql.createConnection({
    host: "stardock.cs.virginia.edu",
    user: "cs4720djw2yw",
    password: "spring2014",
    database: "cs4720djw2yw"
});


// Create the http server. 
var http = require('http');

splitter = function(data){
    var splits = data.split('&');
    var hash = [];
    console.log(splits.length);
    for (i = 0; i < splits.length; i++)
    {
        var iSplit = splits[i].split('=');
        hash[iSplit[0]] = iSplit[1];
    }
    return hash;
}
 
var postHTML =
  '<html><head><title>Post Example</title></head>' +
  '<body>' +
  '<form method="post">' +
  'Your Fist Name: <input name="first_name"><br>' +
  'Your Last Name: <input name="last_name"><br>' +
  '<input type="submit">' +
  '</form>' +
  '</body></html>';
  
 //Below code adapted from http://blog.thekfactor.info/posts/an-introduction-to-node-js-and-handling-post-requests/
var http = require('http');

function splitter(data)  {
	var splits = data.split('&');
    var hash = [];
    console.log(splits.length);
    for (i = 0; i < splits.length; i++)
    {
        var iSplit = splits[i].split('=');
        hash[iSplit[0]] = iSplit[1];
    }
    return hash;
}

 
var postHTML =
  '<html><head><title>Post Example</title></head>' +
  '<body>' +
  '<form method="post">' +
  'Player ID: <input name="player_id"><br>' +
  'Game ID: <input name="game_id"><br>' +
  '<input type="submit">' +
  '</form>' +
  '</body></html>';
 
http.createServer(function (req, res) {
  var body = "";
  req.on('data', function (chunk) {
    body += chunk;
  });
  req.on('end', function () {
    console.log('POSTed: ' + body);
 
    if (body != '')
    {
        var hash = splitter(body);
		 
       var player_id = hash['player_id'];
       var game_id = hash['game_id'];
     
		connection.query("INSERT INTO 'player_junction' ('player_junction_id', 'game_id', 'player_id') VALUES('" + player_id +"', '" + game_id + "');", function (error, rows, fields) {
		   response.writeHead(200, {
			   'Content-Type': 'text/plain'
		   });
       // Send data as JSON string. 
       // Rows variable holds the result of the query. 
       response.write(JSON.stringify(rows));
       response.end();
       });
		
    }
 

  });
}).listen(8888);
console.log("Server running at http://127.0.0.1:8888/");