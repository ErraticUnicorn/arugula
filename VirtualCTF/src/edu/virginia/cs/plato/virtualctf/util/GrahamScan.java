package edu.virginia.cs.plato.virtualctf.util;

import java.util.ArrayList;
import java.util.List;

import com.google.android.gms.maps.model.LatLng;

public class GrahamScan {

	/*Three points are a counter-clockwise turn if ccw > 0, clockwise if
# ccw < 0, and collinear if ccw = 0 because ccw is a determinant that
# gives the signed area of the triangle formed by p1, p2 and p3.
	 */
	public static double ccw(double x1, double y1, double x2, double y2, double x3, double y3){
		return (double) ((x2 - x1)*(y3 - y1) - (y2 - y1)*(x3*x1));
	}
	/*let N           = number of points
 let points[N+1] = the array of points
 swap points[1] with the point with the lowest y-coordinate
 sort points by polar angle with points[1]

 # We want points[0] to be a sentinel point that will stop the loop.
   let points[0] = points[N]

 # M will denote the number of points on the convex hull.
let M = 1
for i = 2 to N:
 # Find next valid point on convex hull.
   while ccw(points[M-1], points[M], points[i]) <= 0:
  if M > 1:
       M -= 1
       # All points are collinear
  else if i == N:
       break
  else
       i += 1

 # Update M and swap points[i] to the correct place.
 M += 1
 swap points[M] with points[i]*/
	public static double[] graham(double[] point){
		
		int n = point.length;
		double[] points = new double[n+2];
		
		for(int i = 0; i < point.length; i++){
			points[i] = point[i];
		}
		
		int index = 1;
		double lowy = points[1];
		double lowx = points[0];
		for(int i = 1; i < points.length; i += 2){
			if(points[i] < lowy){
				index = i;
				lowy = points[i];
				lowx = points[i-1];
			}
			if(points[i] == lowy){
				if(points[i-1] < lowx){
					index = i;
					lowy = points[i];
					lowx = points[i-1];
				}
			}
		}
		points[index] = points[1];
		points[index-1] = points[0];
		points[0] = lowx;
		points[1] = lowy;
		sort(points);
		//We want points[0] to be a sentinel point that will stop the loop.
		//points[n] = points[0];
		//points[n+1] = points[1];
		int m = 3;
		
		for(int i = 5; i < n; i+=2){
			while(ccw(points[m-3], points[m-2], points[m-1], points[m], points[i-1], points[i]) <= 0){
				if(m > 3){
					System.out.println("hi");
					m-= 2;
					//all points are collinear
				}
				else if (i == n){
					break;
				}
				else{
					i += 2;
				}
			}
			m += 2;
			double tmpX = points[m-1];
			double tmpY = points[m];
			
			points[m - 1] = points[i-1];
			points[m] = points[i];
			
			points[i-1] = tmpX;
			points[i] = tmpY;
		}
		
		if(m < n-1) {
			double[] res = new double[m];
			
			for(int j = 0; j < m; j++) {
				res[j] = points[j];
			}
			
			return res;
		}
		return points;	
	}
	
	public static ArrayList<LatLng> graham(List<LatLng> points) {
		double[] res = new double[points.size() * 2];
		
		for(int i = 0; i < points.size(); i++) {
			LatLng p = points.get(i);
			
			res[2 * i] = p.latitude;
			res[2 * i + 1] = p.longitude;
		}
		
		res = graham(res);
		
		ArrayList<LatLng> ress = new ArrayList<LatLng>();
		for(int i = 0; i < res.length / 2; i++) {
			ress.add(new LatLng(res[2*i], res[2*i+1]));
		}
		
		return ress;
	}
	//-1 : o1 < o2
	//0 : o1 == o2
	//+1 : o1 > o2
	public static int polarAngleCompare(double originx, double originy, double x1, double y1, double x2, double y2){
		if(originx == x1){
			x1 += .01;
		}
		if(originx == x2){
			x2 += .01;
		}
		double angle1 = Math.toDegrees(Math.atan((y1-originy)/(x1-originx)));
		double angle2 = Math.toDegrees(Math.atan((y2-originy)/(x2-originx)));
		if (angle1 < angle2){
			return -1;
		}
		if (angle1 == angle2){
			return 0;
		}
		if(angle1 > angle2){
			return 1;
		}
		return 0;
	}
	public static double[] sort (double [] points){
		double tempx;
		double tempy;
		for (int i = 3; i < points.length-4; i+=2)
		{
			//System.out.println(polarAngleCompare(points[0], points[1], points[i-1], points[i], points[i+1], points[i+2]));
			if(polarAngleCompare(points[0], points[1], points[i-1], points[i], points[i+1], points[i+2]) == 1) 
			{
				tempx = points[i-1];
				tempy=points[i];
				points[i]=points[i+2];
				points[i-1] = points[i+1];
				points[i+2] = tempy;
				points[i+1]=tempx;
				if(i-2 == 3){
					i = i;
				} else {
					i -=2;
				}
				//i=-2;
			}
		}
		return points;
	}
}
