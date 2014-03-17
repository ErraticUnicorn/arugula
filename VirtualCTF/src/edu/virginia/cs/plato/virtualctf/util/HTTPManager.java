package edu.virginia.cs.plato.virtualctf.util;

import com.google.gson.JsonArray;

import android.util.Log;

public class HTTPManager {

	public static void getActiveGames() {
		GetJson call = new GetJson(new JsonCallback() {

			@Override
			public void call(JsonArray param) {

				Log.d("VirtualCTF", "Call: " + param.toString());
				
			}
			
		});
		
		call.execute("http://plato.cs.virginia.edu/~jaw3ej/arugula_testing/test/");
	}
	
}
