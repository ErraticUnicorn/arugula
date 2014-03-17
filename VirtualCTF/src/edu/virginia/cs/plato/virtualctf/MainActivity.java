package edu.virginia.cs.plato.virtualctf;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;
import edu.virginia.cs.plato.virtualctf.util.HTTPManager;
import edu.virginia.cs.plsto.virtualctf.R;

public class MainActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		HTTPManager.getActiveGames();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
