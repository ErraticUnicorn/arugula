package edu.virginia.cs.plato.virtualctf;

public class Session {

	private static Session instance;
	
	private Session() {
		
	}
	
	public static Session getInstance() {
		if(instance == null) {
			instance = new Session();
		}
		
		return instance;
	}

	private int playerId;
	private int teamId;
	private int gameId;
	
	public int getTeamId() {
		return teamId;
	}

	public void setTeamId(int teamId) {
		this.teamId = teamId;
	}

	public int getPlayerId() {
		return playerId;
	}

	public void setPlayerId(int playerId) {
		this.playerId = playerId;
	}

	public int getGameId() {
		return gameId;
	}

	public void setGameId(int gameId) {
		this.gameId = gameId;
	}
	
	
}
