package edu.virginia.cs.plato.virtualctf;

import java.util.ArrayList;
import java.util.List;

import android.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.GoogleMap.OnMapClickListener;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.MapsInitializer;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.gson.JsonArray;

import edu.virginia.cs.plato.virtualctf.util.HTTPManager;
import edu.virginia.cs.plato.virtualctf.util.JsonCallback;

public class NewGameBoundsActivity extends FragmentActivity {

	private static GoogleMap map;

	private String name;
	private String pw;

	private List<LatLng> teamOne;
	private List<LatLng> teamTwo;

	private int mode = 0;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_new_game_bounds);

		if (savedInstanceState == null) {
			getFragmentManager().beginTransaction()
			.add(R.id.container, new PlaceholderFragment()).commit();
		}

		Bundle extras = getIntent().getExtras();
		if (extras != null) {
			name = extras.getString("Name");
			//			pw = extras.getString("PW");
		}

		teamOne = new ArrayList<LatLng>();
		teamTwo = new ArrayList<LatLng>();
		
	}

	protected void onStart() {
		super.onStart();

		MapsInitializer.initialize(this);

		getFragmentManager().executePendingTransactions();
		onMapLoad();
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


	public void onMapLoad() {

		// Get a handle to the Map Fragment
		map = ((MapFragment) getFragmentManager()
				.findFragmentById(R.id.map))
				.getMap(); 

		if(map != null) {
			map.setOnMapClickListener(new OnMapClickListener() {

				@Override
				public void onMapClick(LatLng p) {
					if(mode == 1) {
						teamTwo.add(p);
						map.addMarker(new MarkerOptions()
						.position(p)
						.title("Team 2") 
						.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_AZURE)));

					} 
					else if(mode == 0) {
						teamOne.add(p);
						map.addMarker(new MarkerOptions()
						.position(p)
						.title("Team 1")
						.icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_RED)));
					}
				}

			});
		}

	}
	public void onSubmit(View v) {

		if(mode == 1) {
			HTTPManager.createNewGame(new JsonCallback() {

				@Override
				public void call(JsonArray param) {
					goToMain();
				}

			}, name, pw);
		}
		else if(mode == 0) {
			TextView title = (TextView) findViewById(R.id.bounds_title);
			Button sub = (Button) findViewById(R.id.submit_bounds);

			title.setText(R.string.boundsTitle2);
			sub.setText(R.string.create_game);

			mode = 1;
		}

	}

	public void goToMain() {
		Intent i = new Intent(getApplicationContext(), MainActivity.class); 
		startActivity(i);
	}

}
