package edu.virginia.cs.plato.virtualctf.util;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;

import android.os.AsyncTask;
import android.util.Log;

import com.google.gson.JsonArray;
import com.google.gson.JsonParser;


public class GetJson extends AsyncTask<String, String, JsonArray> {

	private JsonCallback callback;
	
	public GetJson(JsonCallback callback_) {
		callback = callback_;
	}
	
	public String getJSONfromURL(String url) {

		// initialize
		InputStream is = null;
		String result = "";

		// http post
		try {
			HttpClient httpclient = new DefaultHttpClient();
			HttpPost httppost = new HttpPost(url);
			HttpResponse response = httpclient.execute(httppost);
			HttpEntity entity = response.getEntity();
			is = entity.getContent();

		} catch (Exception e) {
			Log.e("VirtualCTF", "Error in http connection " + e.toString());
		}

		// convert response to string
		try {
			BufferedReader reader = new BufferedReader(new InputStreamReader(
					is, "iso-8859-1"), 8);
			StringBuilder sb = new StringBuilder();
			String line = null;
			while ((line = reader.readLine()) != null) {
				sb.append(line + "\n");
			}
			is.close();
			result = sb.toString();
		} catch (Exception e) {
			Log.e("VirtualCTF", "Error converting result " + e.toString());
		}

		return result;
	}

	@Override
	protected JsonArray doInBackground(String... params) {
		JsonArray res = new JsonArray();
		String url = params[0];
		try {
			String webJSON = getJSONfromURL(url);
			//Log.d("JSON", webJSON);
			JsonParser parser = new JsonParser();
			res = parser.parse(webJSON).getAsJsonArray();
		} catch (Exception e) {
			Log.e("VirtualCTF", "JSONPARSE:" + e.toString());
		}

		return res;
	}
	
	@Override
	protected void onPostExecute(JsonArray result) {
		callback.call(result);
	}

}