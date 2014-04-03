package edu.virginia.cs.plato.virtualctf;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

import com.google.gson.JsonArray;

import edu.virginia.cs.plato.virtualctf.util.HTTPManager;
import edu.virginia.cs.plato.virtualctf.util.JsonCallback;

public class NewGameBoundsActivity extends FragmentActivity {

	private String name;
	private String pw;

	private boolean teamOne;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_new_game_bounds);

		if (savedInstanceState == null) {
			getSupportFragmentManager().beginTransaction()
			.add(R.id.container, new PlaceholderFragment()).commit();
		}

		Bundle extras = getIntent().getExtras();
		if (extras != null) {
			name = extras.getString("Name");
//			pw = extras.getString("PW");
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.new_game_bounds, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

	/**
	 * A placeholder fragment containing a simple view.
	 */
	public static class PlaceholderFragment extends Fragment {

		public PlaceholderFragment() {
		}

		@Override
		public View onCreateView(LayoutInflater inflater, ViewGroup container,
				Bundle savedInstanceState) {
			View rootView = inflater.inflate(R.layout.fragment_new_game_bounds,
					container, false);
			return rootView;
		}
	}

	public void onSubmit(View v) {
		if(teamOne) {
			HTTPManager.createNewGame(new JsonCallback() {

				@Override
				public void call(JsonArray param) {
					goToMain();
				}

			}, name, pw);
		}
		else {
			TextView title = (TextView) findViewById(R.id.bounds_title);
			Button sub = (Button) findViewById(R.id.submit_bounds);

			title.setText(R.string.boundsTitle2);
			sub.setText(R.string.create_game);

			teamOne = true;
		}

	}

	public void goToMain() {
		Intent i = new Intent(getApplicationContext(), MainActivity.class); 
		startActivity(i);
	}

}
