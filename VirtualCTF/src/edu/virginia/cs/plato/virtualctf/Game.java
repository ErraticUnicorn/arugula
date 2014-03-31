package edu.virginia.cs.plato.virtualctf;

public class Game {

	private int id;
	private String name;
	private String pw;
	private String start;
	private String end;
	public Game(int id, String name, String pw, String start, String end) {
		super();
		this.id = id;
		this.name = name;
		this.pw = pw;
		this.start = start;
		this.end = end;
	}
	public int getId() {
		return id;
	}
	public void setId(int id) {
		this.id = id;
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getPw() {
		return pw;
	}
	public void setPw(String pw) {
		this.pw = pw;
	}
	public String getStart() {
		return start;
	}
	public void setStart(String start) {
		this.start = start;
	}
	public String getEnd() {
		return end;
	}
	public void setEnd(String end) {
		this.end = end;
	}
	
	@Override
	public String toString() {
		return id + ":\t" + name + "\t" + start + "\t to\t" + end;
	}
	
}
