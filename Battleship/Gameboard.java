import java.io.*;
import java.util.Random;

public class Gameboard{
   
   private String gameName;
   String user; 
   String keyBox []; 
   int board [][];
   int hitBoard [][];
   boolean sunk [];
   final int battleship = 6;
   final int carrier = 5;
   final int destroyer = 4;
   final int submarine = 3;
   final int patrol = 2;
   final int miss = 9;
   final int hit = 8;
   
   
   Gameboard(String name){
   
      //SET UP:
      gameName = "Sink that Battleship!!!";
      
      user = name;    
         
      //set board to 0
      board = new int [10][10];
      
      //sets the board to zero 
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            board [lcv][lcv2] = 0; 
         }
      }
      
      hitBoard = new int [10][10];
      
      //sets the hitBoard to zero 
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            hitBoard [lcv][lcv2] = 0; 
         }
      }
      
      sunk = new boolean [5];
      
      //sets the sunk to false
      for(int lcv = 0;lcv < 5;lcv++){
          sunk [lcv] = false; 
      }
      
      keyBox = new String [10]; 
      keyBox[0] ="6 --- BATTLESHIP";
      keyBox[1] ="5 --- CARRIER";
      keyBox[2] ="4 --- DESTROYER";
      keyBox[3] ="3 --- SUBMARINE";
      keyBox[4] ="2 --- PATROL";
      keyBox[5] ="O --- MISS";
      keyBox[6] ="X --- HIT";
      keyBox[7] ="";
      keyBox[8] ="";
      keyBox[9] ="";
      
      setShips();
      
      
   }//end of game board
   
   public void printBoards(){
      //print out both boards the players and where they targeted
      //format correctly make sure 0's do not print out
      //(a-j) (1-10)
      
      printHeader();
      
      //int numbs [] = {1,2,3,4,5,6,7,8,9,10};
      char letts [] = {'a','b','c','d','e','f','g','h','i','j'};
      
      System.out.printf("\n\t\t  MY BOARD\t\t\t\t\tTARGET BOARD\n");
      System.out.printf("    1   2   3   4   5   6   7   8   9   10  \t    1   2   3   4   5   6   7   8   9   10  \tKEY:");
      System.out.printf("\n  -----------------------------------------\t  -----------------------------------------\t---------------\n");
      for(int lcv = 0;lcv < 10;lcv++){
         System.out.printf("%s |", letts[lcv]);
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(board [lcv][lcv2] == 0){
               System.out.printf("   |");
            }
            else if(board [lcv][lcv2] == hit){
               System.out.printf(" X |");
            }
            else{
               System.out.printf(" %d |",board [lcv][lcv2]); 
            }
         }
         System.out.printf("\t%s |", letts[lcv]);
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(hitBoard [lcv][lcv2] == 0){
               System.out.printf("   |");
            }
            else if(hitBoard [lcv][lcv2] == hit){
               System.out.printf(" X |");
            }
            else if(hitBoard [lcv][lcv2] == miss){
               System.out.printf(" O |");
            }
            else{
               System.out.printf(" %d |",hitBoard [lcv][lcv2]); 
            }
         }
         System.out.printf("\t%s", keyBox[lcv]);
         System.out.printf("\n  -----------------------------------------\t  -----------------------------------------\n");
      }
      System.out.printf("\n");
      
   }
   
   private void printHeader(){
   
      System.out.printf("\n");
   
      for(int i = 0; i < 100; i++){
         System.out.printf("=");
      }
      
      System.out.printf("\n\t\t\t\t\t%s\n",gameName);
      
      for(int i = 0; i < 100; i++){
         System.out.printf("=");
      }
      
      System.out.printf("\n");
      
   }//end of printHeader
   
   private void setShips(){
      //private only gameBoard can access it
      //randomly sets ships
      
      Random randomGenerator = new Random ();
      
      //battleship = 6
      getShipPlacement(randomGenerator, battleship);
      
      //carrier = 5
      getShipPlacement(randomGenerator, carrier);
      
      //destroyer = 4
      getShipPlacement(randomGenerator, destroyer);
   
      
      //submarine = 3
      getShipPlacement(randomGenerator, submarine);
      
      
      //patrol = 2
      getShipPlacement(randomGenerator, patrol);
      
           
   }//end of setShips
   
   private void getShipPlacement(Random randomGenerator, int limit){
   
      boolean placed = false; 
      int numbers [] = new int[limit];
      int size = limit-1;
      
      while(placed == false){
      
         int shipX = randomGenerator.nextInt(10);
         int shipY = randomGenerator.nextInt(10);
         int shipPostion = randomGenerator.nextInt(2);
      
         int x = shipX;
         int y = shipY;
      
         //System.out.printf("\n%s\t\t\tSHIP: %d\t\tX: %d\tY: %d\tP: %d\n",user, limit, shipX, shipY, shipPostion);
         
         if(shipPostion == 0){
            
            for(int lcv = 0;lcv < limit;lcv++){
               if(x >= 0 && x <= 9){
                  if(board [x][y] == 0){
                     numbers[lcv] = x;
                     x++;
                  }
                  else{
                     break;
                  }
               }
               else{
                  break;
               }
               if(lcv == size){
                  for(int lcv6 = 0; lcv6 < limit; lcv6++){
                     board[numbers[lcv6]][shipY] = limit;
                  }
                  placed = true;
               }
            }
            
         }//horizontal
         else{
            
            for(int lcv3 = 0;lcv3 < limit;lcv3++){
               if(y >= 0 && y <= 9){
                  if(board [x][y] == 0){
                     numbers[lcv3] = y;
                     y++;
                  }
                  else{
                     break;
                  }
               }
               else{
                  break;
                  
               }
            
               if(lcv3 == size){
                  for(int lcv5 = 0; lcv5 < limit; lcv5++){
                     board[shipX][numbers[lcv5]] = limit;
                  }
                  placed = true;
               }
            }
         }//vertical 
      }//end of while loop
   
   }//end of getShipPlacement
   
   public void setTargetBoard(int x, int y, int value){
      //hits for other player
      
      hitBoard[x][y] = value; 
      
   }
   
   public boolean isHit(int x, int y){
      //checks board to see if location hits a ship
      //if does arks it with an '8'
      //hitBoard[x][y] = 9;
      
      if(board[x][y] == 0){
         return false;
      }
      else{
         board[x][y] = hit;
         return true;
      }
   
   }
   
   public int isShipSunk(){
   
      boolean gone= true; 
   
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(board [lcv][lcv2] == carrier){
               gone= false; 
            } 
         }
      }//carrier
      
      if(gone== true && sunk[carrier-1] == false){
         sunk[carrier-1] = true;
         return carrier; 
      }
      
      gone= true; 
      
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(board [lcv][lcv2] == destroyer){
               gone= false; 
            } 
         }
      }//destroyer
      
      if(gone== true && sunk[destroyer-1] == false){
         sunk[destroyer-1] = true;
         return destroyer; 
      }
      
      gone= true; 
      
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(board [lcv][lcv2] == submarine){
               gone= false; 
            } 
         }
      }//submarine
      
      if(gone== true && sunk[submarine-1] == false){
         sunk[submarine-1] = true;
         return submarine; 
      }
      
      gone= true; 
      
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(board [lcv][lcv2] == patrol){
               gone= false; 
            } 
         }
      }//patrol
      
      if(gone== true && sunk[patrol-1] == false){
         sunk[patrol-1] = true;
         return patrol; 
      }
      
      gone= true; 
      
      return -1; 
      
   }
   
   public boolean isBattleshipSunk(){
      //checks battleship after every isHit 
      boolean gone= true;
      
      for(int lcv = 0;lcv < 10;lcv++){
         for(int lcv2 = 0;lcv2 < 10;lcv2++){
            if(board [lcv][lcv2] == battleship){
               gone= false; 
            } 
         }
      }
      return gone;
   }//end isBattleshipSunk
   
}