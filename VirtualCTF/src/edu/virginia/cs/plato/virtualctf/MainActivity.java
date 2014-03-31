package edu.virginia.cs.plato.virtualctf;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;

import com.google.gson.Gson;
import com.google.gson.JsonArray;

import edu.virginia.cs.plato.virtualctf.util.HTTPManager;
import edu.virginia.cs.plato.virtualctf.util.JsonCallback;
import edu.virginia.cs.plsto.virtualctf.R;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	public void getActiveGames() {

		HTTPManager.getActiveGames(new JsonCallback() {

			@Override
			public void call(JsonArray params) {

				Game[] res = new Game[params.size()];
				Gson gson = new Gson();

				for (int i = 0; i < params.size(); i++) {
					res[i] = gson.fromJson(params.get(i), Game.class);
				}

				String str = "";
				for(Game g : res) {
					str += "\n" + g.toString();
				}
				
			}
			
		});
	}
	
	public void addButton() {
		findViewById(R.id.gameList);
	}
}
