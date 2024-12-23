import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Scanner;

import javax.swing.JFrame;
import javax.swing.JTable;

public class ClueComputer {
    private static int players = 6;
    private static int currentPlayer = 1;
    private static ArrayList<Guess> guesses = new ArrayList<Guess>();
    private static char[][] table;
    private static Guess currentGuess;
    private static final ArrayList<String> suspectList = new ArrayList<String>(Arrays.asList("Mustard", "Plum", "Green", "Peacock", "Scarlett", "White"));
    private static final ArrayList<String> weaponList = new ArrayList<String>(Arrays.asList("Wrench", "Candlestick", "Dagger", "Revolver", "Leadpipe", "Rope"));
    private static final ArrayList<String> roomList = new ArrayList<String>(Arrays.asList("Ball", "Billiard", "Conservatory", "Dining", "Hall", "Kitchen", "Library", "Lounge", "Study"));
    private static final ArrayList<String> fullList = newFullList();

    public static void main(String[] args){
        run();
        //GUIManager.run();
        //run();
        // JFrame frame = new JFrame();
        // String[] columnNames = {"Name", "Age", "Student"};
        // String[][] data = {
        //     {"Ken", "5", "false"},
        //     {"Bob", "6", "true"},
        //     {"Joe", "7", "false"},
        //     {"Mike", "8", "true"},
        //     {"Bill", "9", "false"}
        // };
        // JTable table = new JTable(data, columnNames);
        // frame.add(table);
        // frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        // frame.setSize(400,400);
        // frame.setLocationRelativeTo(null);  
        // frame.setVisible(true);
    }

    public static void run() {
        Scanner s = new Scanner(System.in);
        System.out.print("\nWelcome to ClueComputer.\nEnter number of players: ");
        players = s.nextInt();
        s.nextLine();
        System.out.print("\nNumber your opponents 2-"+(players)+". Player 0 will be the envelope and player 1 will be you.\nI will print out the table at the end of each turn. An X means that \nplayer does not have that card, O means that player has that card. \nHit enter to continue.   ");
        s.nextLine();
        startTable(fullList.size(), players+1);
        while (true){
            System.out.print("\nEnter your next card (or x to continue): ");
            String next = s.nextLine();
            if(next.equals("x")){break;}
            eliminateHorizontally(getIndex(next), 1);
        }
        printTable();
        System.out.print("\nWhich player took the first turn? (whole number): ");
        currentPlayer = s.nextInt();
        s.nextLine();
        currentGuess = new Guess();
        while (true){
            System.out.print("\nEnter Player "+currentPlayer+"'s guess or x to skip: ");
            String guess = s.nextLine();
            parseGuess(guess);
            //System.out.println(currentGuess.toString());
            int showPlayer;
            System.out.print("Which player showed Player "+ currentPlayer +" a card?(0 if no one did): ");
            try {
                showPlayer = s.nextInt();
                s.nextLine();
            } catch(Exception e){
                break;
            }
            currentGuess.player = showPlayer;
            if(showPlayer!=0 && showPlayer!=1){guesses.add(currentGuess);}
            guessElims(showPlayer);
            if(currentPlayer==1 && showPlayer!=0){
                System.out.print("Which card did Player "+showPlayer+" show you? ");
                String str = s.nextLine();
                eliminateHorizontally(getIndex(str), showPlayer);
            }
            checkTable();
            printTable();

            if(currentPlayer==players){currentPlayer=1;}
            else{currentPlayer++;}
        }

        s.close();
    }
    //puts an O in any column where there are all X's
    public static void checkTable(){
        for(int i=0;i<table.length;i++){
            boolean allX = true;
            for(int j=1;j<table[i].length;j++){
                if(table[i][j] != 'X'){
                    allX = false;
                    break;
                }
            } if(allX){
                table[i][0] = 'O';
            }
        }
        checkGuesses();
    }
    //check if we can find new information based on the guesses made earlier in the game
    public static void checkGuesses(){
        boolean changed = false;
        ArrayList<Guess> toRemove = new ArrayList<Guess>();
        for (Guess g : guesses) {
            int p = g.player;
            int s = getIndex(g.suspect);
            int w = getIndex(g.weapon);
            int r = getIndex(g.room);
            if(table[s][p]=='X' && table[w][p]=='X'){
                if(!(table[r][p]=='-')){System.out.println("table["+g.room+"]["+p+"]+ changed to O");}
                eliminateHorizontally(r, p);
                changed = true;
                toRemove.add(g);
                System.out.println(g.toString()+"--> removed, room deducted");
            } else if(table[s][p]=='X' && table[r][p]=='X'){
                if(!(table[w][p]=='-')){System.out.println("table["+g.weapon+"]["+p+"]+ changed to O");}
                eliminateHorizontally(w, p);
                changed = true;
                toRemove.add(g);
                System.out.println(g.toString()+"--> removed, weapon deducted");
            } else if(table[r][p]=='X' && table[w][p]=='X'){
                if(!(table[s][p]=='-')){System.out.println("table["+g.suspect+"]["+p+"]+ changed to O");}
                eliminateHorizontally(s, p);
                changed = true;
                toRemove.add(g);
                System.out.println(g.toString()+"--> removed, suspect deducted");
            } else if (table[s][p]=='O' || table[r][p]=='O' || table[w][p]=='O'){
                System.out.println(g.toString() + " removed due to one being true.");
                toRemove.add(g);
            }
        }
        for(Guess g: toRemove){
            guesses.remove(g);
        }
        if(changed){checkGuesses();}
    }
    //runs currentGuessElim for each player that said they didn't have any of the cards in Guess
    public static void guessElims(int showPlayer){
        for(int i=currentPlayer+1;i!=showPlayer;i++){
            if(i==players+1){i=0;}
            else if(i==currentPlayer){break;}
            else {currentGuessElim(i);};
        }
    }
    //eliminates all 3 values in 1 guess from a player, if they said they didn't have any of these cards
    public static void currentGuessElim(int col){
        table[getIndex(currentGuess.suspect)][col] = 'X';
        table[getIndex(currentGuess.weapon)][col] = 'X';
        table[getIndex(currentGuess.room)][col] = 'X';
    }
    //parses a string with 3 cards separated by spaces into a guess. uses parseWord as well.
    public static Guess parseGuess(String guess){
        currentGuess = new Guess();
        currentGuess.player = currentPlayer;
        String str = guess.substring(0, guess.indexOf(" "));
        guess = guess.substring(guess.indexOf(" ")+1);
        parseWord(str);
        //System.out.println(currentGuess.toString() + "\nparseWord("+str+"), guess=["+guess+"]");
        str = guess.substring(0, guess.indexOf(" "));
        guess = guess.substring(guess.indexOf(" ")+1);
        parseWord(str);
        //System.out.println(currentGuess.toString() + "\nparseWord("+str+"), guess=["+guess+"]");
        parseWord(guess);
        return currentGuess;
    }
    public static void parseWord(String word){
        word = capitalizeWord(word);
        if(suspectList.contains(word)){
            currentGuess.suspect = word;
        } else if (weaponList.contains(word)){
            currentGuess.weapon = word;
        } else {
            currentGuess.room = word;
        }
    }
    
    public static void eliminateHorizontally(int row, int col){
        for (int i=0;i<table[row].length;i++){
            table[row][i] = 'X';
        }
        table[row][col] = 'O';
        //System.out.println(names[row]+", "+col+" eliminated horizontally.");
    }
    public static void printTable(){
        System.out.print("               ");
        for(int i=0; i<players+1;i++){
            System.out.printf("%-3s", i+"");
        }
        System.out.println();
        for(int i=0; i<suspectList.size();i++){
            System.out.printf("%n%-15s", suspectList.get(i));
            for(int j=0;j<table[0].length;j++){
                System.out.print(table[i][j]+"  ");
            }
        }
        System.out.printf("%n%-15s", "------");
        for(int j=0;j<table[0].length;j++){
            System.out.print("---");
        }
        for(int i=0; i<weaponList.size();i++){
            System.out.printf("%n%-15s", weaponList.get(i));
            for(int j=0;j<table[0].length;j++){
                System.out.print(table[i+suspectList.size()][j]+"  ");
            }
        }
        System.out.printf("%n%-15s", "------");
        for(int j=0;j<table[0].length;j++){
            System.out.print("---");
        }
        for(int i=0; i<roomList.size();i++){
            System.out.printf("%n%-15s", roomList.get(i));
            for(int j=0;j<table[0].length;j++){
                System.out.print(table[i+suspectList.size()+weaponList.size()][j]+"  ");
            }
        }
    }
    public static void startTable(int rows, int cols){
        table = new char[rows][cols];
        for(int i = 0;i<table.length;i++){
            if(i != 1){
                for(int j=0;j<table[i].length;j++){
                    table[i][j] = '-';
                }
            } else {
                for(int j=0;j<table[i].length;j++){
                    table[i][j] = 'X';
                }
            } 
        }
    }
    public static ArrayList<String> newFullList(){
        ArrayList<String> list = new ArrayList<>();
        suspectList.forEach(item -> list.add(item));
        weaponList.forEach(item -> list.add(item));
        roomList.forEach(item -> list.add(item));
        return list;
    }
    public static boolean ignoreCaseContains(ArrayList<String> theList, String searchStr) {
        for (String s : theList) {
            if (searchStr.equalsIgnoreCase(s)) {
                return true;
            }
        }
        return false;
    }
    public static int getIndex(String str){
        return fullList.indexOf(capitalizeWord(str));
    }
    public static String capitalizeWord(String word){
        try {
            return word.substring(0, 1).toUpperCase() + word.substring(1).toLowerCase();
        }catch(Exception e){return "EmptyWord";}
    }
    public static int getCardNumbers(){
        return fullList.size();
    }
    public static void setCurrentGuess(String s, String w, String r){
        currentGuess = new Guess(s, w, r, currentPlayer);
    }
    public static void setCurrentPlayer(int p){
        currentPlayer = p;
    }
}