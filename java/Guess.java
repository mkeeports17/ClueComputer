package java;
public class Guess {
    public String suspect;
    public String room;
    public String weapon;
    public int player;
    public Guess(String s, String w, String r, int p){
        room = r;
        suspect = s;
        weapon = w;
        player = p;
    }
    public Guess(){
        suspect = "";
        weapon = "";
        room = "";
        player=0;
    }
    public String toString(){
        return "[P#]"+player+", [S]"+suspect+", [W]"+weapon+", [R]"+room;
    }
}

