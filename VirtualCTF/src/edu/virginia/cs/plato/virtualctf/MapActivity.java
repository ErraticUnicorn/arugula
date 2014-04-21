package edu.virginia.cs.plato.virtualctf;

import android.app.ActionBar;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.DialogFragment;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapFragment;
import com.google.android.gms.maps.MapsInitializer;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;

import edu.virginia.cs.plato.virtualctf.util.HTTPManager;
import edu.virginia.cs.plato.virtualctf.util.IntCallback;
import edu.virginia.cs.plato.virtualctf.util.JsonCallback;

public class MapActivity extends FragmentActivity implements
NavigationDrawerFragment.NavigationDrawerCallbacks {

	private static final long TOLERANCE = 1000;

	private GoogleMap map;
	/**
	 * Fragment managing the behaviors, interactions and presentation of the
	 * navigation drawer.
	 */
	private NavigationDrawerFragment mNavigationDrawerFragment;

	/**
	 * Used to store the last screen title. For use in
	 * {@link #restoreActionBar()}.
	 */
	private CharSequence mTitle;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_map);

		mNavigationDrawerFragment = (NavigationDrawerFragment) getSupportFragmentManager()
				.findFragmentById(R.id.navigation_drawer);
		mTitle = getTitle();

		// Set up the drawer.
		mNavigationDrawerFragment.setUp(R.id.navigation_drawer,
				(DrawerLayout) findViewById(R.id.drawer_layout));
		// Acquire a reference to the system Location Manager
		LocationManager locationManager = (LocationManager) this.getSystemService(Context.LOCATION_SERVICE);

		// Define a listener that responds to location updates
		LocationListener locationListener = new LocationListener() {

			public void onLocationChanged(Location location) {

				// Called when a new location is found by the network location provider.
				LatLng p = new LatLng(location.getLatitude(), location.getLongitude());
				Log.d("CTF", "GPS have!");

				onLocChange(p);

			}

			public void onStatusChanged(String provider, int status, Bundle extras) {}

			public void onProviderEnabled(String provider) {}

			public void onProviderDisabled(String provider) {}
		};

		// Register the listener with the Location Manager to receive location updates
		locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, TOLERANCE, 0f, locationListener);
		Log.d("CTF", "Loc have!");

	}

	protected void onStart() {
		super.onStart();

		MapsInitializer.initialize(this);

		getFragmentManager().executePendingTransactions();
		onMapLoad();
	}

	public void onMapLoad() {

		// Get a handle to the Map Fragment
		map = ((MapFragment) getFragmentManager()
				.findFragmentById(R.id.map))
				.getMap(); 

		map.animateCamera(CameraUpdateFactory.zoomBy(21));
	}

	@Override
	public void onNavigationDrawerItemSelected(int position) {
		// update the main content by replacing fragments
		FragmentManager fragmentManager = getSupportFragmentManager();
		fragmentManager
		.beginTransaction()
		.replace(R.id.container,
				PlaceholderFragment.newInstance(position + 1)).commit();
	}

	public void onSectionAttached(int number) {
		switch (number) {
		case 1:
			mTitle = getString(R.string.title_section1);
			break;
		case 2:
			mTitle = getString(R.string.title_section2);
			break;
		case 3:
			mTitle = getString(R.string.title_section3);
			break;
		}
	}

	public void restoreActionBar() {
		ActionBar actionBar = getActionBar();
		actionBar.setNavigationMode(ActionBar.NAVIGATION_MODE_STANDARD);
		actionBar.setDisplayShowTitleEnabled(true);
		actionBar.setTitle(mTitle);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		if (!mNavigationDrawerFragment.isDrawerOpen()) {
			// Only show items in the action bar relevant to this screen
			// if the drawer is not showing. Otherwise, let the drawer
			// decide what to show in the action bar.
			getMenuInflater().inflate(R.menu.map, menu);
			restoreActionBar();
			return true;
		}
		return super.onCreateOptionsMenu(menu);
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
		/**
		 * The fragment argument representing the section number for this
		 * fragment.
		 */
		private static final String ARG_SECTION_NUMBER = "section_number";

		/**
		 * Returns a new instance of this fragment for the given section number.
		 */
		public static PlaceholderFragment newInstance(int sectionNumber) {
			PlaceholderFragment fragment = new PlaceholderFragment();
			Bundle args = new Bundle();
			args.putInt(ARG_SECTION_NUMBER, sectionNumber);
			fragment.setArguments(args);
			return fragment;
		}

		public PlaceholderFragment() {
		}

		@Override
		public View onCreateView(LayoutInflater inflater, ViewGroup container,
				Bundle savedInstanceState) {
			View rootView = inflater.inflate(R.layout.fragment_map, container,
					false);
			TextView textView = (TextView) rootView
					.findViewById(R.id.section_label);
			textView.setText(R.string.google);
			return rootView;
		}

		@Override
		public void onAttach(Activity activity) {
			super.onAttach(activity);
			((MapActivity) activity).onSectionAttached(getArguments().getInt(
					ARG_SECTION_NUMBER));
		}
	}

	public void goToMain(View v) {
		Intent i = new Intent(getApplicationContext(), MainActivity.class); 
		startActivity(i);
	}

	public void share(View v) {
		Intent shareIntent = new Intent();
		shareIntent.setAction(Intent.ACTION_SEND); 
		shareIntent.setType("text/plain");
		shareIntent.putExtra(Intent.EXTRA_TEXT, "I'm playing a game of VirtualCTF!");
		startActivity(Intent.createChooser(shareIntent, "Share your game"));
	}

	/*
	 * 1: T1 wins
	 * 2: T2 wins
	 * 3: T1 scores
	 * 4: T2 scores
	 * 5: Freed
	 * 6: You tagged
	 * 7: Pick flag
	 */

	private int getId(int res) {
		switch(res) {
		case 1:
			break;
		case 2:
			break;
		case 3:
			break;
		case 4:
			break;
		case 5:
			return R.string.free;
		case 6:
			return R.string.tagged_other;
		case 7:
			return R.string.flag_get;
		}

		return -1;
	}

	public void onLocChange(final LatLng p) {

		Log.d("VirtualCTF", "Now at point " + p.latitude + ", " + p.longitude);

		map.animateCamera(CameraUpdateFactory.newLatLng(p));

		HTTPManager.postGPS(new IntCallback() {

			@Override
			public void call(int param) {
				// TODO Auto-generated method stub
//				final int id = getId(param);
//
//				if(id != -1) {
//					PopUp pop = new PopUp() {
//						protected int getMsg() {
//							return id;
//						}
//					};
//
//					pop.show(getFragmentManager(), "Notification");
//				}
				

			}

		}, Session.getInstance().getPlayerId(), p.latitude, p.longitude);

		show();
	}

	public void show() {
		map.clear();

		final int team = Session.getInstance().getTeamId();
		final int game = Session.getInstance().getGameId();

		HTTPManager.getAllies(new JsonCallback() {

			@Override
			public void call(JsonArray param) {
				// TODO Auto-generated method stub

				for(JsonElement p : param) {
					JsonObject ally = p.getAsJsonObject();

					Log.d("CTFs","Allies!!!");
					
					map.addMarker(new MarkerOptions()
					.icon(BitmapDescriptorFactory.fromResource(team == 1 ? R.drawable.blood : R.drawable.crip))
					.position(new LatLng(ally.get("lat").getAsDouble(), ally.get("long").getAsDouble())));
				}

			}

		}, game, team);

		HTTPManager.getEnemies(new JsonCallback() {

			@Override
			public void call(JsonArray param) {
				// TODO Auto-generated method stub

				for(JsonElement p : param) {
					JsonObject ally = p.getAsJsonObject();

					map.addMarker(new MarkerOptions()
					.icon(BitmapDescriptorFactory.fromResource(team == 1 ? R.drawable.crip : R.drawable.blood))
					.position(new LatLng(ally.get("lat").getAsDouble(), ally.get("long").getAsDouble())));
				}

			}

		}, game, team);

		HTTPManager.getAlliedFlags(new JsonCallback() {

			@Override
			public void call(JsonArray param) {
				// TODO Auto-generated method stub

				for(JsonElement p : param) {
					JsonObject ally = p.getAsJsonObject();

					Log.d("CTFs","AlliesFlags!!!");
					
					map.addMarker(new MarkerOptions()
					.icon(BitmapDescriptorFactory.fromResource(team == 1 ? R.drawable.redflag : R.drawable.blueflag))
					.position(new LatLng(ally.get("lat").getAsDouble(), ally.get("long").getAsDouble())));
				}

			}

		}, game, team);

		HTTPManager.getEnemyFlags(new JsonCallback() {

			@Override
			public void call(JsonArray param) {
				// TODO Auto-generated method stub

				for(JsonElement p : param) {
					JsonObject ally = p.getAsJsonObject();

					map.addMarker(new MarkerOptions()
					.icon(BitmapDescriptorFactory.fromResource(team == 1 ? R.drawable.blueflag : R.drawable.redflag))
					.position(new LatLng(ally.get("lat").getAsDouble(), ally.get("long").getAsDouble())));
				}

			}

		}, game, team);

	} 

	public static class PopUp extends DialogFragment {

		protected int getMsg() {
			return -1;
		}

		@Override
		public Dialog onCreateDialog(Bundle savedInstanceState) {
			// Use the Builder class for convenient dialog construction
			AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
			builder.setMessage(getMsg())
			.setPositiveButton(R.string.event_select, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int id) {
					// FIRE ZE MISSILES!
				}
			});
			// Create the AlertDialog object and return it
			return builder.create();
		}
	}
}
