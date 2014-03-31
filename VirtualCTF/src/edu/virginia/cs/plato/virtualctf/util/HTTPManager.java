package edu.virginia.cs.plato.virtualctf.util;


public class HTTPManager {

	public static void getActiveGames(JsonCallback callback) {
		
		GetJson call = new GetJson(callback);
		
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula_testing/test/");
	}
	
}
