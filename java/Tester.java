//package java;
public class Tester {
    public static void main(String[] args){
        ClueComputer.startTable(ClueComputer.getCardNumbers(), 7);

        testGuessElims();

        ClueComputer.printTable();
    }

    public static void testParseGuess(){
        Guess g = ClueComputer.parseGuess("Kitchen White Dagger");
        System.out.println(g);
    }
    public static void testEliminateHorizontally(){
        ClueComputer.eliminateHorizontally(4, 2);
        ClueComputer.eliminateHorizontally(5, 4);
    }
    public static void testCurrentGuessElim(){
        ClueComputer.setCurrentGuess("Green", "Dagger", "Dining");
        ClueComputer.currentGuessElim(3);
        ClueComputer.currentGuessElim(5);
    }
    public static void testGuessElims(){
        ClueComputer.setCurrentGuess("Green", "Dagger", "Dining");
        ClueComputer.setCurrentPlayer(4);
        ClueComputer.guessElims(2);
    }
}
