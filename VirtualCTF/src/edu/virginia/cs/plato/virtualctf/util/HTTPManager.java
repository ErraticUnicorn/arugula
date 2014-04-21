package edu.virginia.cs.plato.virtualctf.util;

import java.util.List;

import com.google.android.gms.maps.model.LatLng;



public class HTTPManager {

	public static void getActiveGames(JsonCallback callback) {
		
		GetJson call = new GetJson(callback);
		
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/getall/");
	}

	public static void createNewGame(JsonCallback callback, String name, List<LatLng> boundsT1, List<LatLng> boundsT2) {

		String t1 = "";
		for(LatLng p : boundsT1) {
			t1 += p.latitude + "," + p.longitude + ";";
		}
		t1 = t1.substring(0, t1.length() - 2);
		
		String t2 = "";
		for(LatLng p : boundsT2) {
			t2 += p.latitude + "," + p.longitude + ";";
		}
		t2 = t2.substring(0, t2.length() - 2);
		
		GetJson call = new GetJson(callback);
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/creategame/" + name + "/" + t1 + "/" + t2);
	}

	public static void getGame(JsonCallback callback, int gameId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/getgame/"+gameId);
	}

	public static void joinGame(IntCallback callback, int gameId, int teamId) {
		
		GetInt call = new GetInt(callback);

//		call.execute("http://nodejs-arugula.rhcloud.com/"+gameId+"/"+playerId);
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/joingame/"+gameId+"/"+teamId);
	}

	public static void postGPS(IntCallback callback, int playerId, double lat, double lng) {
		
		GetInt call = new GetInt(callback);

//		call.execute("http://nodejs-arugula.rhcloud.com/"+gameId+"/"+playerId);
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/gpsupdate/"+playerId+"/"+lat+"/"+lng);
	}
 
	public static void findBuilding(JsonCallback callback, String building) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~cs4720s14beet/tracks/view/"+building);
	}

	public static void postBounds(JsonCallback callback, int gameId, int team, double lat, double lng) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~cs4720s14beet/tracks/view/");
		
	}
	public static void getAllies(JsonCallback callback, int gameId, int teamId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/alliedplayers/" + gameId + "/" + teamId);
		
	}
	public static void getEnemies(JsonCallback callback, int gameId, int teamId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/enemyplayers/" + gameId + "/" + teamId);
		
	}
	public static void getAlliedFlags(JsonCallback callback, int gameId, int teamId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/alliedflags/" + gameId + "/" + teamId);
		
	}
	public static void getEnemyFlags(JsonCallback callback, int gameId, int teamId) {
		
		GetJson call = new GetJson(callback);

		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula/enemyflags/" + gameId + "/" + teamId);
		
	}
}
