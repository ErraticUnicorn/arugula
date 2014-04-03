package edu.virginia.cs.plato.virtualctf;

import android.app.ActionBar.LayoutParams;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;

import com.google.gson.Gson;
import com.google.gson.JsonArray;

import edu.virginia.cs.plato.virtualctf.util.HTTPManager;
import edu.virginia.cs.plato.virtualctf.util.JsonCallback;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		getActiveGames();
	}

	@Override
	protected void onStart() {
		super.onStart();
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

				for(Game g : res) {
					makeButton(g);
				} 

			}

		});
	}

	public void addButton() {
		findViewById(R.id.gameList);
	}

	public void onCreateNewGame(View v) {
		Intent i = new Intent(getApplicationContext(), NewGameActivity.class); 
		startActivity(i);
	}

	public void onJoinGame() {
		Intent i = new Intent(getApplicationContext(), MapActivity.class); 
		startActivity(i);
	}

	public void makeButton(final Game g) {
		Button btn = new Button(this);
		btn.setId(g.getId());
		btn.setText(g.getName());
		LinearLayout ll = (LinearLayout)findViewById(R.id.gameList);
		LayoutParams lp = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
		ll.addView(btn, lp);
		btn.setOnClickListener(new View.OnClickListener() {
			public void onClick(View view) {
				//Calls join method
				HTTPManager.joinGame(new JsonCallback() {

					@Override
					public void call(JsonArray param) {
						
						onJoinGame();
						
					}
					
				}, g.getId(), 3);
			}
		});
	}

}
