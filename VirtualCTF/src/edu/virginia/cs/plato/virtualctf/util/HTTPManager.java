package edu.virginia.cs.plato.virtualctf.util;

import com.google.gson.JsonArray;


public class HTTPManager {

	public static void getActiveGames(JsonCallback callback) {
		
		GetJson call = new GetJson(callback);
		
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula_testing/test/");
	}

	public static void createNewGame(JsonCallback callback, String name, String pw) {
		
		GetJson call = new GetJson(callback);
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula_testing/test/" + name + "/" + pw);
	}

	public static void joinGame(JsonCallback callback, int gameId, int playerId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://nodejs-arugula.rhcloud.com/"+gameId+"/"+playerId);
	}
	
}
