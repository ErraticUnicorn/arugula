var http = require('http'),
mysql = require("mysql");
url = require( "url" );

// Create the connection. 
// Data is default to new mysql installation and should be changed according to your configuration. 
var connection = mysql.createConnection({
    host: "stardock.cs.virginia.edu",
    user: "cs4720djw2yw",
    password: "spring2014",
    database: "cs4720djw2yw"
});

http.createServer(function (request, response) {
	var _get = url.parse(request.url, true).query;
	var userid = _get['gameid'];
	connection.query("SELECT * FROM favoriteMovies WHERE title ='"+ userid + "';", function (error, rows, fields){
	response.writeHead(200, {
            'Content-Type': 'text/plain'
        });
		// Send data as JSON string. 
        // Rows variable holds the result of the query. 
        response.write(JSON.stringify(rows));
        response.end();
	
	});
}).listen(8888);

console.log("Server running at http://127.0.0.1:8888/");