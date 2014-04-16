package edu.virginia.cs.plato.virtualctf.util;



public class HTTPManager {

	public static void getActiveGames(JsonCallback callback) {
		
		GetJson call = new GetJson(callback);
		
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/getall/");
	}

	public static void createNewGame(JsonCallback callback, String name, String pw) {
		
		GetJson call = new GetJson(callback);
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/creategame/" + name);
	}

	public static void getGame(JsonCallback callback, int gameId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/getgame/"+gameId);
	}

	public static void joinGame(JsonCallback callback, int gameId, int playerId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://nodejs-arugula.rhcloud.com/"+gameId+"/"+playerId);
	}
	
}
